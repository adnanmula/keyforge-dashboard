<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleLocks implements DeckAnalyzeRule
{
    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Lock';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        if ($deck->cards()->has('Tezmal', 3)) {
            $card1 = $deck->cards()->get('Tezmal');

            $r[] = [
                'description' => 'Triple cosecha con tezmal no te permite seleccionar ninguna casa, game over',
                'cards' => [
                    $card1->name => $card1->serializedName,
                ],
            ];
        }

        if ($deck->cards()->has('Tezmal', 2) && $deck->cards()->has('Rocket Boots')) {
            $card1 = $deck->cards()->get('Tezmal');
            $card2 = $deck->cards()->get('Rocket Boots');

            $r[] = [
                'description' => 'Triple cosecha con tezmal no te permite seleccionar ninguna casa, game over',
                'cards' => [
                    $card1->name => $card1->serializedName,
                    $card2->name => $card2->serializedName,
                ],
            ];
        }

        if (\count($r) === 0) {
            return null;
        }

        return [
            'category' => self::CATEGORY,
            'subcategory' => self::SUBCATEGORY,
            'results' => $r,
        ];
    }
}
