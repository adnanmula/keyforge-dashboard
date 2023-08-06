<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleAmberGeneration;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleLocks;

final readonly class DeckAnalyzeService
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new DeckAnalyzeRuleAmberGeneration(),
            new DeckAnalyzeRuleLocks(),
        ];
    }

    public function execute(KeyforgeDeck $deck): array
    {
        $results = [];

        foreach ($this->rules as $rule) {
            $r = $rule->execute($deck);

            if (null === $r) {
                continue;
            }

            $results[$r['category']][$r['subcategory']] = $r['results'];
        }

        return $results;
    }
}
