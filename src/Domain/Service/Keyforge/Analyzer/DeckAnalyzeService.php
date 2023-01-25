<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeAreaDamageRule;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeCreaturePositionRule;

final readonly class DeckAnalyzeService
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new DeckAnalyzeCreaturePositionRule(),
            new DeckAnalyzeAreaDamageRule(),
        ];
    }

    public function execute(KeyforgeDeck $deck): array
    {
        $results = [];

        /** @var DeckAnalyzeRule $rule */
        foreach ($this->rules as $rule) {
            $r = $rule->execute($deck);

            if (null === $r) {
                continue;
            }

            $results[$r->key] = $r->results;
        }

        return $results;
    }
}
