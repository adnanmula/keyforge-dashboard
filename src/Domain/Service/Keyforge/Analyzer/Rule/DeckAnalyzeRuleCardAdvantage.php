<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleCardAdvantage implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Ventaja de cartas';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        $r[] = $this->ruleHasCards($deck, 'Triggerea fácilmente la Library Card', 'Library Card', 'Dark Æmber Vault');
        $r[] = $this->ruleHasCards($deck, 'Triggerea fácilmente el Library Access', 'Library Access', 'Dark Æmber Vault');
        $r[] = $this->ruleHasCards($deck, 'Triggerea fácilmente el Auto-Encoder', 'Auto-Encoder', 'Punctuated Equilibrium');
        $r[] = $this->ruleHasCards($deck, 'Triggerea fácilmente el Auto-Encoder', 'Auto-Encoder', 'Novu Dynamo');
        $r[] = $this->ruleHasCards($deck, 'Recursión QMechs', 'Fangtooth Cavern', 'Q-Mechs');
        $r[] = $this->ruleHasCards($deck, 'Recursión Rad Penny', 'Fangtooth Cavern', 'Rad Penny');

        $r = \array_values(\array_filter($r));

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
