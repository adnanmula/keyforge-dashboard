<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Service\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class UpdateDeckWinRateService
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeDeckUserDataRepository $deckUserDataRepository,
        private KeyforgeGameRepository $gameRepository,
        private UserRepository $userRepository,
        private KeyforgeUserRepository $keyforgeUserRepository,
    ) {}

    public function execute(Uuid $deckId): void
    {
        [$deck, $userData, $games, $players, $friends] = $this->data($deckId);

        $winStats = [];

        /** @var KeyforgeGame $game */
        foreach ($games as $game) {
            $winStats[$game->winner()->value()] = ['wins' => 0, 'losses' => 0, 'winsVsFriends' => 0, 'lossesVsFriends' => 0, 'winsVsUser' => 0, 'lossesVsUser' => 0];
            $winStats[$game->loser()->value()] = ['wins' => 0, 'losses' => 0, 'winsVsFriends' => 0, 'lossesVsFriends' => 0, 'winsVsUser' => 0, 'lossesVsUser' => 0];
        }

        foreach (\array_keys($winStats) as $userId) {
            foreach ($games as $game) {
                if (false === $game->approved() || $game->isSoloPlay()|| $game->isMirror()) {
                    continue;
                }

                if ($game->winner()->value() === $userId && $game->winnerDeck()->value() === $deck->id()->value()) {
                    $hisFriends = $friends[$userId] ?? [];

                    $winStats[$userId]['wins']++;

                    if (\in_array($game->loser()->value(), $players, true)) {
                        $winStats[$userId]['winsVsUser']++;
                    }

                    if (\in_array($game->loser()->value(), $hisFriends, true)) {
                        $winStats[$userId]['winsVsFriends']++;
                    }
                }

                if ($game->loser()->value() === $userId && $game->loserDeck()->value() === $deck->id()->value()) {
                    $hisFriends = $friends[$userId] ?? [];

                    $winStats[$userId]['losses']++;

                    if (\in_array($game->loser()->value(), $players, true)) {
                        $winStats[$userId]['lossesVsUser']++;
                    }

                    if (\in_array($game->loser()->value(), $hisFriends, true)) {
                        $winStats[$userId]['lossesVsFriends']++;
                    }
                }
            }
        }

        $this->deckUserDataRepository->remove($deckId);

        foreach ($winStats as $userId => $winStat) {
            $userDatum = $userData[$userId] ?? null;

            if (null === $userDatum) {
                $userDatum = KeyforgeDeckUserData::from($deck->id(), Uuid::from($userId), 0, 0, 0, 0, 0, 0);
            }

            $userDatum->setWins(
                $winStat['wins'],
                $winStat['losses'],
                $winStat['winsVsFriends'],
                $winStat['lossesVsFriends'],
                $winStat['winsVsUser'],
                $winStat['lossesVsUser'],
            );

            $this->deckUserDataRepository->save($userDatum);
        }
    }

    private function data(Uuid $deckId): array
    {
        $deck = $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $deck) {
            throw new \Exception('Deck not found');
        }

        $userData = $this->deckUserDataRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('deck_id'), new StringFilterValue($deck->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        $games = $this->gameRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::OR,
                    new Filter(new FilterField('winner_deck'), new StringFilterValue($deck->id()->value()), FilterOperator::EQUAL),
                    new Filter(new FilterField('loser_deck'), new StringFilterValue($deck->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        $players = $this->keyforgeUserRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NULL),
                ),
            ),
        );

        $players = \array_map(static fn (KeyforgeUser $u) => $u->id()->value(), $players);

        $userDataIndexed = [];
        $friends = [];

        foreach ($userData as $userDatum) {
            $userDataIndexed[$userDatum->userId()->value()] = $userDatum;

            $friends[$userDatum->userId()->value()] = \array_map(
                static fn (array $f) => $f['friend_id'],
                $this->userRepository->friends($userDatum->userId(), false),
            );
        }

        return [$deck, $userDataIndexed, $games, $players, $friends];
    }
}
