<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAmberControl implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const string CATEGORY = 'Combo';
    public const string SUBCATEGORY = 'Control de ámbar';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('Protege parte de lo capturado', 'Curia Saurus', 'Amphora Captura');
        $r[] = $this->ruleHasCards('Mucho poder de robo', 'Scrivener Favian', 'Amphora Captura');
        $r[] = $this->ruleHasCards('Trigger fácil de Rad Penny', 'Rad Penny', 'Seeker Needle');
        $r[] = $this->ruleHasCards('Recursión del efecto de Ronnie', 'Kompsos Haruspex', 'Ronnie Wristclocks');
        $r[] = $this->ruleHasCards('Recursión del efecto Inforno', 'Kompsos Haruspex', 'Infurnace');
        $r[] = $this->ruleHasCards('Recursión de Inforno', 'Screaming Cave', 'Infurnace');
        $r[] = $this->ruleHasCards('Recursión de Inforno', 'Infurnace', 'Universal Recycle Bin');
        $r[] = $this->ruleHasCards('Recursión fácil de Bo Nithing', 'Safe House', 'Bo Nithing');
        $r[] = $this->ruleHasCards('Recursión de Banda', 'Kymoor Eclipse', 'Chain Gang');
        $r[] = $this->ruleHasCards('Recursión de Ronnie', 'Kymoor Eclipse', 'Ronnie Wristclocks');
        $r[] = $this->ruleHasCards('Recursión de Bo Nithing', 'Kymoor Eclipse', 'Bo Nithing');
        $r[] = $this->ruleHasCards('Recursión de Banda', 'Hit and Run', 'Chain Gang');
        $r[] = $this->ruleHasCards('Recursión de Ronnie', 'Hit and Run', 'Ronnie Wristclocks');
        $r[] = $this->ruleHasCards('Recursión de Bo Nithing', 'Hit and Run', 'Bo Nithing');
        $r[] = $this->ruleHasCards('Captura y obtiene ámbar', 'City-State Interest', 'Bury Riches');
        $r[] = $this->ruleHasCards('Recursión de Inforno', 'Infurnace', 'Hysteria');

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
