<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Service;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class FixturesGeneratorService
{
    public function execute(KeyforgeCompetition $competition): array
    {
        $fixtures = $this->generateFixtures($competition);

        $lastFixture = \end($fixtures);
        $position = $lastFixture->position() + 1;
        $reference = (int) \substr($lastFixture->reference(), \strlen($lastFixture->reference()) - 1, \strlen($lastFixture->reference())) + 1;

        if ($competition->type() === CompetitionType::ROUND_ROBIN_2) {
            $secondHalfFixtures = [];

            $count = 0;
            foreach ($fixtures as $fixture) {
                $secondHalfFixtures[] = new KeyforgeCompetitionFixture(
                    Uuid::v4(),
                    $fixture->competitionId(),
                    'Jornada ' . $reference,
                    \array_reverse($fixture->users()),
                    $fixture->type(),
                    $position,
                    new \DateTimeImmutable(),
                    null,
                    null,
                    [],
                );

                $count++;

                $halfCount = \count($competition->users()) % 2 === 0
                    ? \ceil(\count($competition->users()) / 2)
                    : \ceil(\count($competition->users()) / 2) - 1;

                if ($count >= $halfCount) {
                    $reference++;
                    $count = 0;
                }

                $position++;
            }

            $fixtures = \array_merge($fixtures, $secondHalfFixtures);
        }

        return $fixtures;
    }

    /** @return array<KeyforgeCompetitionFixture> */
    private function generateFixtures(KeyforgeCompetition $competition): array
    {
        $users = \array_map(static fn (Uuid $id): string => $id->value(), $competition->users());

        \shuffle($users);

        if (\count($users) % 2 !== 0) {
            $users[] = null;
        }

        $fixtures = [];
        $halfCount = \count($users) / 2;
        $position = 0;

        for ($i = 0; $i < \count($users) - 1; $i++) {
            for ($j = 0; $j <= $halfCount - 1; $j++) {
                $user1 = $users[$j];
                $user2 = $users[\count($users) - $j - 1];

                if (null === $user1 || null === $user2) {
                    continue;
                }

                $fixtures[] = new KeyforgeCompetitionFixture(
                    Uuid::v4(),
                    $competition->id(),
                    'Jornada ' . ($i + 1),
                    [$user1, $user2],
                    CompetitionFixtureType::BEST_OF_1,
                    $position,
                    new \DateTimeImmutable(),
                    null,
                    null,
                    [],
                );

                $position++;
            }

            $users = $this->rotate($users);
        }

        return $fixtures;
    }

    private function rotate(array $users): array {
        $firstPlayer = $users[0];
        unset($users[0]);

        $lastPlayer = \array_pop($users);

        return [$firstPlayer, $lastPlayer, ...$users];
    }
}
