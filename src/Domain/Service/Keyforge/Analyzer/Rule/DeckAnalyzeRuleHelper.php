<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;

trait DeckAnalyzeRuleHelper
{
    private function ruleHasCards(KeyforgeDeck $deck, string $description, string ...$cardNames): ?array
    {
        $cards = [];

        foreach ($cardNames as $cardName) {
            if (false === $deck->data->cards->has($cardName)) {
                return null;
            }

            $card = $deck->data->cards->get($cardName);
            $cards[$card->name] = $card->serializedName;
        }

        return [
            'description' => $description,
            'cards' => $cards,
        ];
    }
}
