<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

trait DeckAnalyzeRuleHelper
{
    private function ruleHasCards(string $description, string ...$cardNames): ?array
    {
        $cards = [];

        foreach ($cardNames as $cardName) {
            if (false === $this->deck->data()->cards->has($cardName)) {
                return null;
            }

            $card = $this->deck->data()->cards->get($cardName);
            $cards[$card->name] = $card->serializedName;
        }

        return [
            'description' => $description,
            'cards' => $cards,
        ];
    }
}
