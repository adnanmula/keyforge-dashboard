<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAmberControl implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Control de ámbar';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        $r[] = $this->ruleHasCards($deck, 'Protege parte de lo capturado', 'Curia Saurus', 'Amphora Captura');
        $r[] = $this->ruleHasCards($deck, 'Mucho poder de robo', 'Scrivener Favian', 'Amphora Captura');
        $r[] = $this->ruleHasCards($deck, 'Recursión del efecto de Ronnie', 'Kompsos Haruspex', 'Ronnie Wristclocks');
        $r[] = $this->ruleHasCards($deck, 'Recursión del efecto Inforno', 'Kompsos Haruspex', 'Infurnace');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Inforno', 'Screaming Cave', 'Infurnace');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Inforno', 'Infurnace', 'Universal Recycle Bin');
        $r[] = $this->ruleHasCards($deck, 'Trigger fácil de Rad Penny', 'Rad Penny', 'Seeker Needle');
        $r[] = $this->ruleHasCards($deck, 'Recursión fácil de Bo Nithing', 'Safe House', 'Bo Nithing');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Banda', 'Kymoor Eclipse', 'Chain Gang');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Ronnie', 'Kymoor Eclipse', 'Ronnie Wristclocks');
        $r[] = $this->ruleHasCards($deck, 'Recursión de Bo Nithing', 'Kymoor Eclipse', 'Bo Nithing');

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
