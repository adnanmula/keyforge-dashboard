<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class ChainsCalculator
{
    public function __construct(
        private KeyforgeUserRepository $userRepository,
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeGameRepository $gameRepository,
    ) {}

    public function execute(Uuid $user1, Uuid $user2, Uuid $deck1, Uuid $deck2): array
    {
        $this->assert($user1, $user2, $deck1, $deck2);

        $gamesWithUsers = $this->gameRepository->byUser($user1, $user2);
        $gamesWithDecks = $this->gameRepository->byUsersAndDecks([$user1, $user2], [$deck1, $deck2]);

        $indexedGamesWithUser = [
            $user1->value() => [
                'wins' => 0,
                'losses' => 0,
            ],
            $user2->value() => [
                'wins' => 0,
                'losses' => 0,
            ],
        ];

        foreach ($gamesWithUsers as $gameWithUser) {
            $indexedGamesWithUser[$gameWithUser->winner()->value()] = [
                'wins' => $indexedGamesWithUser[$gameWithUser->winner()->value()]['wins'] + 1,
                'losses' => $indexedGamesWithUser[$gameWithUser->winner()->value()]['losses'] + 1,
            ];

            $indexedGamesWithUser[$gameWithUser->loser()->value()] = [
                'wins' => $indexedGamesWithUser[$gameWithUser->loser()->value()]['wins'] + 1,
                'losses' => $indexedGamesWithUser[$gameWithUser->loser()->value()]['losses'] + 1,
            ];
        }

        $indexedGamesWithDecks = [
            $deck1->value() => [
                'wins' => 0,
                'losses' => 0,
            ],
            $deck2->value() => [
                'wins' => 0,
                'losses' => 0,
            ],
        ];

        foreach ($gamesWithDecks as $gameWithDecks) {
            $indexedGamesWithDecks[$gameWithDecks->winnerDeck()->value()] = [
                'wins' => $indexedGamesWithDecks[$gameWithDecks->winnerDeck()->value()]['wins'] + 1,
                'losses' => $indexedGamesWithDecks[$gameWithDecks->winnerDeck()->value()]['losses'] + 1,
            ];

            $indexedGamesWithDecks[$gameWithDecks->loserDeck()->value()] = [
                'wins' => $indexedGamesWithDecks[$gameWithDecks->loserDeck()->value()]['wins'] + 1,
                'losses' => $indexedGamesWithDecks[$gameWithDecks->loserDeck()->value()]['losses'] + 1,
            ];
        }

        $user1WR = $this->winRate($indexedGamesWithUser[$user1->value()]['wins'], $indexedGamesWithUser[$user1->value()]['losses']);
        $user2WR = $this->winRate($indexedGamesWithUser[$user2->value()]['wins'], $indexedGamesWithUser[$user2->value()]['losses']);
        $deck1WR = $this->winRate($indexedGamesWithDecks[$deck1->value()]['wins'], $indexedGamesWithDecks[$deck1->value()]['losses']);
        $deck2WR = $this->winRate($indexedGamesWithDecks[$deck2->value()]['wins'], $indexedGamesWithDecks[$deck2->value()]['losses']);

        $userWinRateDifference1 = $user1WR - $user2WR;
        $deckWinRateDifference1 = $deck1WR - $deck2WR;

        $userWinRateDifference2 = $user2WR - $user1WR;
        $deckWinRateDifference2 = $deck2WR - $deck1WR;

        $c = 20;

        $chainsUser1 = (int) \floor(($userWinRateDifference1 + $deckWinRateDifference1) / $c);
        $chainsUser2 = (int) \floor(($userWinRateDifference2 + $deckWinRateDifference2) / $c);

        return [
            'chains_user_1' => $chainsUser1,
            'chains_user_2' => $chainsUser2,
        ];
    }

    private function assert(Uuid $user1, Uuid $user2, Uuid $deck1, Uuid $deck2): void
    {
        $user1Entity = $this->userRepository->byId($user1);
        $user2Entity = $this->userRepository->byId($user2);

        $deck1Entity = $this->deckRepository->byId($deck1);
        $deck2Entity = $this->deckRepository->byId($deck2);

        if (null === $user1Entity || null === $user2Entity || null === $deck1Entity || null === $deck2Entity) {
            throw new \InvalidArgumentException();
        }
    }

    private function winRate(int $wins, int $losses): float
    {
        $games = $wins + $losses;

        if ($games === 0) {
            return 0;
        }

        return \round($wins / $games * 100, 2);
    }
}
