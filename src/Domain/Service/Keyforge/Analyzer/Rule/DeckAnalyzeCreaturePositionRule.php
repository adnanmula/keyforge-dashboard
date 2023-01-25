<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeResult;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeThreats;

final class DeckAnalyzeCreaturePositionRule implements DeckAnalyzeRule
{
    public const RULE = 'Posicionamiento de criaturas';

    public function execute(KeyforgeDeck $deck): ?DeckAnalyzeResult
    {
        $r = [];

        if ($deck->data->cards->has(['Mini Groupthink Tank'])) {
            $card = $deck->data->cards->get('Mini Groupthink Tank');
            $r[DeckAnalyzeThreats::CAN_DAMAGE_IF_NEIGHBORS->value][] = [
                'name' => $card->name,
                'serializedName' => $card->serializedName,
            ];
        }

        if ($deck->data->cards->has(['Groupthink Tank'])) {
            $card = $deck->data->cards->get('Groupthink Tank');
            $r[DeckAnalyzeThreats::CAN_DAMAGE_IF_NEIGHBORS->value][] = [
                'name' => $card->name,
                'serializedName' => $card->serializedName,
            ];
        }

        if ($deck->data->cards->has(['Kymoor Eclipse'])) {
            $card = $deck->data->cards->get('Kymoor Eclipse');
            $r[DeckAnalyzeThreats::CAN_LIFT_CREATURES->value][] = [
                'name' => $card->name,
                'serializedName' => $card->serializedName,
            ];
        }

        if (\count($r) === 0) {
            return null;
        }

        return new DeckAnalyzeResult(self::RULE, $r);
    }
}
