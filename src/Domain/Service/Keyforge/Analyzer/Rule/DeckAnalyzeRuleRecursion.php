<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleRecursion implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Control';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        $r[] = $this->ruleHasCards($deck, 'Recursión de CTW', 'Control the Weak', 'Dominator Bauble', 'Witch of the Eye');
        $r[] = $this->ruleHasCards($deck, 'Recursión de CTW', 'Control the Weak', 'Deipno Spymaster', 'Witch of the Eye');
        $r[] = $this->ruleHasCards($deck, 'Recursión de CTW', 'Control the Weak', 'Screaming Cave');
        $r[] = $this->ruleHasCards($deck, 'Alzaos en turno de cualquier casa', 'Masterplan', 'Arise!');
        $r[] = $this->ruleHasCards($deck, 'Alzaos en turno de cualquier casa', 'Masterplan', 'Arise!');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Ronnie', 'Ronnie Wristclocks', 'Screaming Cave');

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
