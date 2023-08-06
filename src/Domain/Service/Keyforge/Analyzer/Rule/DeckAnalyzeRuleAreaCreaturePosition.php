<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAreaCreaturePosition implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Posicionamiento de criaturas';
    public const SUBCATEGORY = 'Lo aprovecha';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        $r[] = $this->ruleHasCards($deck, 'Para hacer daño', 'Groupthink Tank');
        $r[] = $this->ruleHasCards($deck, 'Para hacer daño', 'Mini Groupthink Tank');
        $r[] = $this->ruleHasCards($deck, 'Para devolver al mazo', 'Kymoor Eclipse');

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
