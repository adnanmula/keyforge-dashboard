<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleAmberControl;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleAmberGeneration;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleAreaCreaturePosition;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleAreaDamageBenefits;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleCardAdvantage;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleKeyControl;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleKeyForging;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule\DeckAnalyzeRuleLocks;

final readonly class DeckAnalyzeService
{
    private array $rules;

    public function __construct()
    {
        $this->rules = [
            new DeckAnalyzeRuleAmberGeneration(),
            new DeckAnalyzeRuleLocks(),
            new DeckAnalyzeRuleCardAdvantage(),
            new DeckAnalyzeRuleKeyForging(),
            new DeckAnalyzeRuleAmberControl(),
            new DeckAnalyzeRuleKeyControl(),
            new DeckAnalyzeRuleAreaDamageBenefits(),
            new DeckAnalyzeRuleAreaCreaturePosition(),
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
