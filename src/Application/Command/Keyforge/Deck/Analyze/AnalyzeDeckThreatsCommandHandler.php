<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Analyze;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;

final class AnalyzeDeckThreatsCommandHandler
{
    public function __construct(private readonly ImportDeckService $importDeckService)
    {
    }

    public function __invoke(AnalyzeDeckThreatsCommand $command): array
    {
        $deck = $this->importDeckService->execute($command->deckId, null);

        $creaturePosition = [];
        $nonDestructiveRemoval = [];
        $artifactsRemoval = [];
        $areaDamage = [];

        if ($this->hasCard(['Mini Groupthink Tank'], $deck)) {
            $creaturePosition['Puede hacer mucho daño si vecinos comparten casa'][] = 'Mini Groupthink Tank';
        }

        if ($this->hasCard(['Groupthink Tank'], $deck)) {
            $creaturePosition['Puede hacer mucho daño si vecinos comparten casa'][] = 'Groupthink Tank';
        }

        if ($this->hasCard(['Kymoor Eclipse'], $deck)) {
            $creaturePosition['Puede levantar criaturas en flancos'][] = 'Kymoor Eclipse';
            $nonDestructiveRemoval['Puede levantar criaturas en flancos'][] = 'Kymoor Eclipse';
        }

        if ($deck->extraData()['deck']['artifactControl'] === 0) {
            $artifactsRemoval['No tiene ningun tipo de control de artefactos'][] = '';
        }

        if ($this->hasCard(['Kymoor Eclipse'], $deck)) {
            $areaDamage['Puede aprovechar el daño en area'][] = 'Oleada cur';
        }

        return [
            'deck_id' => $deck->id()->value(),
            'deck_name' => $deck->name(),
            'deck_sas' => $deck->sas(),
            'detail' => [
                'Posicionamiento de criaturas' => $creaturePosition,
                'Eliminación no destructiva' => $nonDestructiveRemoval,
                'Artefactos' => $artifactsRemoval,
                'Daño en area' => $areaDamage,
            ],
        ];
    }

    private function hasCard(array $cards, KeyforgeDeck $deck): bool
    {
        foreach ($deck->extraData()['deck']['housesAndCards'] as $house) {
            foreach ($house['cards'] as $card) {
                if (\in_array($card['cardTitle'], $cards, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
