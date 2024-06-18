<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Service\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class UpdateDeckWinRateService
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeDeckUserDataRepository $deckUserDataRepository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function execute(Uuid $deckId, ?Uuid $owner): void
    {
        if (null === $owner) {
            $owner = Uuid::null();
        }

        [$deck, $userData, $games] = $this->data($deckId);
return;
        $wins = 0;
        $losses = 0;
        $winsVsFriends = 0;
        $lossesVsFriends = 0;
        $winsVsUser = 0;
        $lossesVsUser = 0;

        /** @var KeyforgeGame $game */
        foreach ($games as $game) {
            if ($game->winnerDeck()->equalTo($deckId)) {

                if ($winner)


                $wins++;


            }

            if ($game->loserDeck()->equalTo($deckId)) {
                $losses++;
            }
        }

        dd($deck, $deckId, $owner, $userData, $games);
    }

    public function data(Uuid $deckId): array
    {
        $deck = $this->deckRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        )[0] ?? null;

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

        return [$deck, $userData, $games];
    }
}