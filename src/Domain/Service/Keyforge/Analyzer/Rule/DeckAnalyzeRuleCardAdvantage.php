<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleCardAdvantage implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Ventaja de cartas';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('Triggerea fácilmente la Library Card', 'Library Card', 'Dark Æmber Vault');
        $r[] = $this->ruleHasCards('Triggerea fácilmente el Library Access', 'Library Access', 'Dark Æmber Vault');
        $r[] = $this->ruleHasCards('Triggerea fácilmente el Auto-Encoder', 'Auto-Encoder', 'Punctuated Equilibrium');
        $r[] = $this->ruleHasCards('Triggerea fácilmente el Auto-Encoder', 'Auto-Encoder', 'Novu Dynamo');
        $r[] = $this->ruleHasCards('Recursión QMechs', 'Fangtooth Cavern', 'Q-Mechs');
        $r[] = $this->ruleHasCards('Recursión Rad Penny', 'Fangtooth Cavern', 'Rad Penny');

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
