<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAreaCreaturePosition implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const string CATEGORY = 'Posicionamiento de criaturas';
    public const string SUBCATEGORY = 'Lo aprovecha';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('Para hacer daño', 'Groupthink Tank');
        $r[] = $this->ruleHasCards('Para hacer daño', 'Mini Groupthink Tank');
        $r[] = $this->ruleHasCards('Para devolver al mazo', 'Kymoor Eclipse');

        $r = \array_values(\array_filter($r));

        if (\count($r) === 0) {
            return null;
        }

        return [
            'category' => self::CATEGORY,
            'subcategory' => self::SUBCATEGORY,
            'results' => $r,
            'deck' => $this->deck->id()->value(),
        ];
    }
}
