<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckUserDataRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagActionCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberBonusHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberBonusLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberExpectedHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAmberExpectedLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagAntiSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArchiveCardCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagArtifactCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureControlHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureControlLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagCreatureProtectionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagDisruptionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEffectivePowerHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEfficiencyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagEfficiencyLow;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasAnomaly;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasBoardWipes;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasKeyCheats;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasLegacy;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasMaverick;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagHasScalingAmberControl;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagRecursionHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagSynergyHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Tag\KeyforgeTagUpgradeCountHigh;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeCards;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;

final readonly class DeckApplyPredefinedTagsService
{
    public function __construct(
        private KeyforgeDeckRepository $repository,
        private KeyforgeDeckUserDataRepository $userDataRepository,
    ) {}

    public function execute(Uuid $id): void
    {
        $deck = $this->repository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringFilterValue($id->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        )[0] ?? null;

        if (null === $deck) {
            return;
        }

        $newTags = [];

        [$maverickCount, $legacyCount, $anomalyCount] = $this->specialCardsCount($deck->cards());

        $newTags[] = $this->tagActionCount($deck);
        $newTags[] = $this->tagAmberControl($deck);
        $newTags[] = $this->tagAntiSynergy($deck);
        $newTags[] = $this->tagArchiveCardCount($deck);
        $newTags[] = $this->tagArtifactControl($deck);
        $newTags[] = $this->tagArtifactCount($deck);
        $newTags[] = $this->tagBonusAmber($deck);
        $newTags[] = $this->tagCreatureControl($deck);
        $newTags[] = $this->tagCreatureCount($deck);
        $newTags[] = $this->tagCreatureProtection($deck);
        $newTags[] = $this->tagDisruption($deck);
        $newTags[] = $this->tagEffectivePower($deck);
        $newTags[] = $this->tagEfficiency($deck);
        $newTags[] = $this->tagExpectedAmber($deck);
        $newTags[] = $this->tagHasAnomaly($anomalyCount);
        $newTags[] = $this->tagHasBoardWipes($deck);
        $newTags[] = $this->tagHasKeyCheats($deck);
        $newTags[] = $this->tagHasLegacy($legacyCount);
        $newTags[] = $this->tagHasMaverick($maverickCount);
        $newTags[] = $this->tagHasScalingAmberControl($deck);
        $newTags[] = $this->tagRecursion($deck);
        $newTags[] = $this->tagSynergy($deck);
        $newTags[] = $this->tagUpgradeCount($deck);

        $draftDecks = [
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
        ];

        if (\in_array($deck->id()->value(), $draftDecks, true)) {
            $newTags = [];
        }

        $userData = $this->userDataRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('deck_id'),
                        new StringFilterValue($id->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );

        foreach ($userData as $userDatum) {
            $userDatum->setTags(...$this->mergeTags($userDatum->tags, \array_filter($newTags)));
            $this->userDataRepository->save($userDatum);
        }
    }

    private function mergeTags(array $currentTags, array $newTags): array
    {
        return \array_values(\array_unique(
            \array_merge($currentTags, \array_map(static fn (KeyforgeDeckTag $tag): string => $tag->id->value(), $newTags)),
        ));
    }

    private function specialCardsCount(KeyforgeCards $cards): array
    {
        $maverickCount = 0;
        $legacyCount = 0;
        $anomalyCount = 0;

        $cards = array_merge(
            $cards->firstPodCards,
            $cards->secondPodCards,
            $cards->thirdPodCards,
        );

        foreach ($cards as $card) {
            if ($card->isLegacy) {
                $legacyCount++;
            }

            if ($card->isMaverick) {
                $maverickCount++;
            }

            if ($card->isAnomaly) {
                $anomalyCount++;
            }
        }

        return [$maverickCount, $legacyCount, $anomalyCount];
    }

    private function tagHasMaverick(int $maverick): ?KeyforgeDeckTag
    {
        if ($maverick > 0) {
            return new KeyforgeTagHasMaverick();
        }

        return null;
    }

    private function tagHasAnomaly(int $anomaly): ?KeyforgeDeckTag
    {
        if ($anomaly > 0) {
            return new KeyforgeTagHasAnomaly();
        }

        return null;
    }

    private function tagHasLegacy(int $legacy): ?KeyforgeDeckTag
    {
        if ($legacy > 0) {
            return new KeyforgeTagHasLegacy();
        }

        return null;
    }

    private function tagEfficiency(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $efficiency = $deck->stats()->efficiency;

        if ($efficiency >= 15) {
            return new KeyforgeTagEfficiencyHigh();
        }

        if ($efficiency <= -1) {
            return new KeyforgeTagEfficiencyLow();
        }

        return null;
    }

    private function tagArtifactCount(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $artifacts = $deck->stats()->artifactCount;

        if ($artifacts >= 6) {
            return new KeyforgeTagArtifactCountHigh();
        }

        return null;
    }

    private function tagHasKeyCheats(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $keyCheats = $deck->stats()->keyCheatCount;

        if ($keyCheats > 0) {
            return new KeyforgeTagHasKeyCheats();
        }

        return null;
    }

    private function tagEffectivePower(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $effectivePower = $deck->stats()->effectivePower;

        if ($effectivePower > 110) {
            return new KeyforgeTagEffectivePowerHigh();
        }

        return null;
    }

    private function tagBonusAmber(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $bonusAmber = $deck->stats()->rawAmber;

        if ($bonusAmber >= 15) {
            return new KeyforgeTagAmberBonusHigh();
        }

        if ($bonusAmber <= 5) {
            return new KeyforgeTagAmberBonusLow();
        }

        return null;
    }

    private function tagExpectedAmber(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $expectedAmber = $deck->stats()->expectedAmber;

        if ($expectedAmber >= 25) {
            return new KeyforgeTagAmberExpectedHigh();
        }

        if ($expectedAmber <= 10) {
            return new KeyforgeTagAmberExpectedLow();
        }

        return null;
    }

    private function tagActionCount(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        if ($deck->stats()->actionCount >= 18) {
            return new KeyforgeTagActionCountHigh();
        }

        return null;
    }

    private function tagCreatureCount(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $creatures = $deck->stats()->creatureCount;

        if ($creatures >= 22) {
            return new KeyforgeTagCreatureCountHigh();
        }

        return null;
    }

    private function tagArchiveCardCount(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->cardArchiveCount;

        if ($value >= 6) {
            return new KeyforgeTagArchiveCardCountHigh();
        }

        return null;
    }

    private function tagUpgradeCount(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $creatures = $deck->stats()->upgradeCount;

        if ($creatures >= 9) {
            return new KeyforgeTagUpgradeCountHigh();
        }

        return null;
    }

    private function tagAmberControl(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->amberControl;

        if ($value >= 15) {
            return new KeyforgeTagAmberControlHigh();
        }

        if ($value < 2) {
            return new KeyforgeTagAmberControlLow();
        }

        return null;
    }

    private function tagCreatureControl(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->creatureControl;

        if ($value >= 15) {
            return new KeyforgeTagCreatureControlHigh();
        }

        if ($value < 4) {
            return new KeyforgeTagCreatureControlLow();
        }

        return null;
    }

    private function tagArtifactControl(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->artifactControl;

        if ($value >= 2) {
            return new KeyforgeTagArtifactControlHigh();
        }

        if ($value <= 0) {
            return new KeyforgeTagArtifactControlLow();
        }

        return null;
    }

    private function tagCreatureProtection(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->creatureProtection;

        if ($value >= 6) {
            return new KeyforgeTagCreatureProtectionHigh();
        }

        return null;
    }

    private function tagRecursion(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->recursion;

        if ($value >= 6) {
            return new KeyforgeTagRecursionHigh();
        }

        return null;
    }

    private function tagDisruption(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->disruption;

        if ($value >= 9) {
            return new KeyforgeTagDisruptionHigh();
        }

        return null;
    }

    private function tagHasScalingAmberControl(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $cards = \array_merge(
            $deck->cards()->firstPodCards,
            $deck->cards()->secondPodCards,
            $deck->cards()->thirdPodCards,
        );

        foreach ($cards as $card) {
            if (\in_array($card->serializedName, KeyforgeCards::SCALING_AMBER_CONTROL)) {
                return new KeyforgeTagHasScalingAmberControl();
            }
        }

        return null;
    }

    private function tagHasBoardWipes(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $cards = \array_merge(
            $deck->cards()->firstPodCards,
            $deck->cards()->secondPodCards,
            $deck->cards()->thirdPodCards,
        );

        foreach ($cards as $card) {
            if (\in_array($card->serializedName, KeyforgeCards::BOARD_CLEARS)) {
                return new KeyforgeTagHasBoardWipes();
            }
        }

        return null;
    }

    private function tagSynergy(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->synergyRating;

        if ($value >= 15) {
            return new KeyforgeTagSynergyHigh();
        }

        return null;
    }

    private function tagAntiSynergy(KeyforgeDeck $deck): ?KeyforgeDeckTag
    {
        $value = $deck->stats()->antiSynergyRating;

        if ($value >= 2) {
            return new KeyforgeTagAntiSynergyHigh();
        }

        return null;
    }
}
