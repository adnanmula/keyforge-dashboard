<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject;

use AdnanMula\Cards\Application\Service\Json;

final readonly class KeyforgeDeckStats implements \JsonSerializable
{
    private function __construct(
        public int $sas,
        public int $previousSasRating,
        public int $previousMajorSasRating,
        public float $sasPercentile,
        public int $sasVersion,
        public int $aercScore,
        public int $aercVersion,
        public float $amberControl,
        public float $artifactControl,
        public float $expectedAmber,
        public float $creatureControl,
        public float $efficiency,
        public float $recursion,
        public float $disruption,
        public int $effectivePower,
        public float $creatureProtection,
        public float $other,
        public int $rawAmber,
        public int $totalPower,
        public int $totalArmor,
        public float $efficiencyBonus,
        public int $creatureCount,
        public int $actionCount,
        public int $artifactCount,
        public int $upgradeCount,
        public int $cardDrawCount,
        public int $cardArchiveCount,
        public int $keyCheatCount,
        public int $boardClearCount,
        public array $boardClearCards,
        public int $scalingAmberControlCount,
        public array $scalingAmberControlCards,
        public int $synergyRating,
        public int $antiSynergyRating,
        public ?\DateTimeImmutable $lastSasUpdate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sas: $data['sas'],
            previousSasRating: $data['previous_sas_rating'],
            previousMajorSasRating: $data['previous_major_sas_rating'],
            sasPercentile: (float) $data['sas_percentile'],
            sasVersion: $data['sas_version'],
            aercScore: $data['aerc_score'],
            aercVersion: $data['aerc_version'],
            amberControl: (float) $data['amber_control'],
            artifactControl: (float) $data['artifact_control'],
            expectedAmber: (float) $data['expected_amber'],
            creatureControl: (float) $data['creature_control'],
            efficiency: (float) $data['efficiency'],
            recursion: (float) $data['recursion'],
            disruption: (float) $data['disruption'],
            effectivePower: (int) $data['effective_power'],
            creatureProtection: (float) $data['creature_protection'],
            other: (float) $data['other'],
            rawAmber: (int) $data['raw_amber'],
            totalPower: $data['total_power'],
            totalArmor: $data['total_armor'],
            efficiencyBonus: (float) $data['efficiency_bonus'],
            creatureCount: $data['creature_count'],
            actionCount: $data['action_count'],
            artifactCount: $data['artifact_count'],
            upgradeCount: $data['upgrade_count'],
            cardDrawCount: $data['card_draw_count'],
            cardArchiveCount: $data['card_archive_count'],
            keyCheatCount: $data['key_cheat_count'],
            boardClearCount: $data['board_clear_count'],
            boardClearCards: Json::decode($data['board_clear_cards']),
            scalingAmberControlCount: $data['scaling_amber_control_count'],
            scalingAmberControlCards: Json::decode($data['scaling_amber_control_cards']),
            synergyRating: $data['synergy_rating'],
            antiSynergyRating: $data['anti_synergy_rating'],
            lastSasUpdate: null === ($data['last_sas_update'] ?? null) ? null : new \DateTimeImmutable($data['last_sas_update']),
        );
    }


    public static function fromDokData(array $data, array $scalingAmberCards, array $boardClearCards): self
    {
        $deck = $data['deck'];

        $boardClearCount = [];
        $scalingAmberControlCount = [];

        foreach ($data['deck']['housesAndCards'] as $houseCards) {
            foreach ($houseCards['cards'] as $card) {
                $urlPieces = explode('/', $card['cardTitleUrl']);
                $serializedName = explode('.', end($urlPieces))[0];

                if (\in_array($serializedName, $boardClearCards, true)) {
                    $boardClearCount[] = $card['cardTitle'];
                }

                if (\in_array($serializedName, $scalingAmberCards, true)) {
                    $scalingAmberControlCount[] = $card['cardTitle'];
                }
            }
        }

        return new self(
            sas: $deck['sasRating'] ?? 0,
            previousSasRating: $deck['previousSasRating'] ?? 0,
            previousMajorSasRating: $deck['previousMajorSasRating'] ?? 0,
            sasPercentile: $deck['sasPercentile'] ?? 0,
            sasVersion: $data['sasVersion'] ?? 0,
            aercScore: $deck['aercScore'] ?? 0,
            aercVersion: $deck['aercVersion'] ?? 0,
            amberControl: $deck['amberControl'] ?? 0,
            artifactControl: $deck['artifactControl'] ?? 0,
            expectedAmber: $deck['expectedAmber'] ?? 0,
            creatureControl: $deck['creatureControl'] ?? 0,
            efficiency: $deck['efficiency'] ?? 0,
            recursion: $deck['recursion'] ?? 0,
            disruption: $deck['disruption'] ?? 0,
            effectivePower: $deck['effectivePower'] ?? 0,
            creatureProtection: $deck['creatureProtection'] ?? 0,
            other: $deck['other'] ?? 0,
            rawAmber: $deck['rawAmber'] ?? 0,
            totalPower: $deck['totalPower'] ?? 0,
            totalArmor: $deck['totalArmor'] ?? 0,
            efficiencyBonus: $deck['efficiencyBonus'] ?? 0,
            creatureCount: $deck['creatureCount'] ?? 0,
            actionCount: $deck['actionCount'] ?? 0,
            artifactCount: $deck['artifactCount'] ?? 0,
            upgradeCount: $deck['upgradeCount'] ?? 0,
            cardDrawCount: $deck['cardDrawCount'] ?? 0,
            cardArchiveCount: $deck['cardArchiveCount'] ?? 0,
            keyCheatCount: $deck['keyCheatCount'] ?? 0,
            boardClearCount: \count($boardClearCount),
            boardClearCards: $boardClearCount,
            scalingAmberControlCount: \count($scalingAmberControlCount),
            scalingAmberControlCards: $scalingAmberControlCount,
            synergyRating: $deck['synergyRating'] ?? 0,
            antiSynergyRating: $deck['antisynergyRating'] ?? 0,
            lastSasUpdate: null === ($deck['lastSasUpdate'] ?? null) ? null : new \DateTimeImmutable($deck['lastSasUpdate']),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'sas' => $this->sas,
            'previousSasRating' => $this->previousSasRating,
            'previousMajorSasRating' => $this->previousMajorSasRating,
            'sasPercentile' => \round($this->sasPercentile, 2),
            'sasVersion' => $this->sasVersion,
            'aercScore' => $this->aercScore,
            'aercVersion' => $this->aercVersion,
            'amberControl' => \round($this->amberControl, 2),
            'artifactControl' => \round($this->artifactControl, 2),
            'expectedAmber' => \round($this->expectedAmber, 2),
            'creatureControl' => \round($this->creatureControl, 2),
            'efficiency' => \round($this->efficiency, 2),
            'recursion' => \round($this->recursion, 2),
            'disruption' => \round($this->disruption, 2),
            'effectivePower' => \round($this->effectivePower, 2),
            'creatureProtection' => \round($this->creatureProtection, 2),
            'other' => \round($this->other, 2),
            'rawAmber' => $this->rawAmber,
            'totalPower' => $this->totalPower,
            'totalArmor' => $this->totalArmor,
            'efficiencyBonus' => \round($this->efficiencyBonus, 2),
            'creatureCount' => $this->creatureCount,
            'actionCount' => $this->actionCount,
            'artifactCount' => $this->artifactCount,
            'upgradeCount' => $this->upgradeCount,
            'cardDrawCount' => $this->cardDrawCount,
            'cardArchiveCount' => $this->cardArchiveCount,
            'keyCheatCount' => $this->keyCheatCount,
            'boardClearCount' => $this->boardClearCount,
            'boardClearCards' => $this->boardClearCards,
            'scalingAmberControlCount' => $this->scalingAmberControlCount,
            'scalingAmberControlCards' => $this->scalingAmberControlCards,
            'synergyRating' => $this->synergyRating,
            'antiSynergyRating' => $this->antiSynergyRating,
            'lastSasUpdate' => $this->lastSasUpdate?->format('Y-m-d'),
        ];
    }
}
