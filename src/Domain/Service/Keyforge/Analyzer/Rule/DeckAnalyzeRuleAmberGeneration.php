<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAmberGeneration implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Generación de  ámbar';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('BRIG, te dobla el dinero cuando estas en posición de forjar y se queda el sobrante', 'Binate Rupture', 'Interdimensional Graft');
        $r[] = $this->ruleHasCards('Cosecha hasta 6 veces con Drummernaut', 'Drummernaut', 'Ganger Chieftain');
        $r[] = $this->ruleHasCards('Cosecha hasta 6 veces con Drummernaut', 'Drummernaut', 'Mega Ganger Chieftain');
        $r[] = $this->ruleHasCards('Captura y lo manda al otro campo', 'Deusillus', 'Exile');
        $r[] = $this->ruleHasCards('Captura, exalta y obtiene el ámbar', 'Tribute', 'Exile');
        $r[] = $this->ruleHasCards('Captura, exalta y lo manda al otro campo', 'Tribute', 'Sic Semper Tyrannosaurus');
        $r[] = $this->ruleHasCards('Captura y lo manda al otro campo', 'Crassosaurus', 'Sic Semper Tyrannosaurus');
        $r[] = $this->ruleHasCards('Prepara hasta seis veces a Rex', 'Cincinnatus Rex', 'The Golden Spiral');
        $r[] = $this->ruleHasCards('Prepara hasta seis veces a Rex', 'Cincinnatus Rex', 'Legatus Raptor');
        $r[] = $this->ruleHasCards('Permite usar a Rex sin depender de las criaturas enemigas', 'Livia the Elder', 'Cincinnatus Rex', 'Legatus Raptor');
        $r[] = $this->ruleHasCards('Permite usar a Rex sin depender de las criaturas enemigas', 'Livia the Elder', 'Cincinnatus Rex', 'The Golden Spiral');
        $r[] = $this->ruleHasCards('Genera 5 de Ámbar', 'Mega Narp', 'The Flex');
        $r[] = $this->ruleHasCards('Cosecha hasta 6 veces seguidas con el Squadron', 'Duskwitch', 'Skybooster Squadron');
        $r[] = $this->ruleHasCards('Burst', 'Song of the Wild', 'Ghosthawk');
        $r[] = $this->ruleHasCards('Burst', 'Song of the Wild', 'Dark Harbinger');
        $r[] = $this->ruleHasCards('Burst', 'Commpod', 'Crystal Hive');
        $r[] = $this->ruleHasCards('Burst', 'Combat Pheromones', 'Crystal Hive');
        $r[] = $this->ruleHasCards('Burst', 'Hysteria', 'A Fair Game');
        $r[] = $this->ruleHasCards('Burst', 'Hecatomb', 'Arise!');
        $r[] = $this->ruleHasCards('Burst', 'The Ulfberht Device', 'Allusions of Grandeur');
        $r[] = $this->ruleHasCards('Burst', 'Loot the Bodies', 'Coward’s End');
        $r[] = $this->ruleHasCards('Recursion de criaturas de marte + cosechas', 'Witch of the Eye', 'Brain Stem Antenna');
        $r[] = $this->ruleHasCards('Información para hacer más efectivo el CTW', 'A Fair Game', 'Control the Weak');
        $r[] = $this->ruleHasCards('Control y recursión del aguijón', 'The Sting', 'Whispering Reliquary');
        $r[] = $this->ruleHasCards('Control y recursión del aguijón', 'The Sting', 'Snudge');
        $r[] = $this->ruleHasCards('Control y recursión del aguijón', 'The Sting', 'Vezyma Thinkdrone');
        $r[] = $this->ruleHasCards('Control y recursión del aguijón', 'The Sting', 'Barehanded');
        $r[] = $this->ruleHasCards('Doble de beneficio', 'Obsidian Forge', 'Soul Snatcher');

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
