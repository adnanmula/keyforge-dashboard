<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeResult;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeThreats;

final class DeckAnalyzeAreaDamageRule implements DeckAnalyzeRule
{
    public const RULE = 'Daño en area';

    public function execute(KeyforgeDeck $deck): ?DeckAnalyzeResult
    {
        $r = [];

        if ($deck->data->cards->has(['Cleansing Wave'])) {
            $r[DeckAnalyzeThreats::CAN_BENEFIT_FROM_AREA_DAMAGE->value][] = 'Cleansing Wave';
        }

        if (\count($r) === 0) {
            return null;
        }

        return new DeckAnalyzeResult(self::RULE, $r);
    }
}
