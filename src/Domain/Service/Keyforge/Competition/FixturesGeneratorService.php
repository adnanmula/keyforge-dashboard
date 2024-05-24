<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Competition;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionFixture;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionFixtureType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class FixturesGeneratorService
{
    public function execute(KeyforgeCompetition $competition, CompetitionFixtureType $fixtureType): array
    {
        $fixtures = $this->generateFixtures($competition, $fixtureType);

        if ($competition->type() === CompetitionType::ROUND_ROBIN_2) {
            $secondHalfFixtures = $this->reverseFixtures($competition, $fixtures);
            $fixtures = \array_merge($fixtures, $secondHalfFixtures);
        }

        if ($competition->type() === CompetitionType::ROUND_ROBIN_3) {
            $secondHalfFixtures = $this->reverseFixtures($competition, $fixtures);
            $thirdHalfFixtures = $this->reverseFixtures($competition, $secondHalfFixtures);
            $fixtures = \array_merge($fixtures, $secondHalfFixtures, $thirdHalfFixtures);
        }

        if ($competition->type() === CompetitionType::ROUND_ROBIN_4) {
            $secondHalfFixtures = $this->reverseFixtures($competition, $fixtures);
            $thirdHalfFixtures = $this->reverseFixtures($competition, $secondHalfFixtures);
            $fourthHalfFixtures = $this->reverseFixtures($competition, $thirdHalfFixtures);
            $fixtures = \array_merge($fixtures, $secondHalfFixtures, $thirdHalfFixtures, $fourthHalfFixtures);
        }

        return $fixtures;
    }

    /** @return array<KeyforgeCompetitionFixture> */
    private function generateFixtures(KeyforgeCompetition $competition, CompetitionFixtureType $fixtureType): array
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
                    $fixtureType,
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

    private function rotate(array $users): array
    {
        $firstPlayer = $users[0];
        unset($users[0]);

        $lastPlayer = \array_pop($users);

        return [$firstPlayer, $lastPlayer, ...$users];
    }

    /** @return array<KeyforgeCompetitionFixture> */
    private function reverseFixtures(KeyforgeCompetition $competition, array $fixtures): array
    {
        $lastFixture = \end($fixtures);
        $position = $lastFixture->position() + 1;

        $referenceParts = \explode(' ', \trim($lastFixture->reference()));
        $reference = (int) \end($referenceParts) + 1;

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

        return $secondHalfFixtures;
    }
}
