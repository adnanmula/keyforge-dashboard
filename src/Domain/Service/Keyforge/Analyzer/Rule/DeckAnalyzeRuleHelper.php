<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

trait DeckAnalyzeRuleHelper
{
    private function ruleHasCards(string $description, string ...$cardNames): ?array
    {
        $cards = [];

        foreach ($cardNames as $cardName) {
            if (false === $this->deck->cards()->has($cardName)) {
                return null;
            }

            $card = $this->deck->cards()->get($cardName);
            $cards[$card->name] = $card->serializedName;
        }

        return [
            'description' => $description,
            'cards' => $cards,
        ];
    }
}
