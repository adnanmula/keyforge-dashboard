<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleAmberGeneration implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Generación de  ámbar';

    public function execute(KeyforgeDeck $deck): ?array
    {
        $r = [];

        $r[] = $this->ruleHasCards($deck, 'BRIG, te dobla el dinero cuando estas en posición de forjar y se queda el sobrante', 'Binate Rupture', 'Interdimensional Graft');
        $r[] = $this->ruleHasCards($deck, 'Cosecha hasta 6 veces con Drummernaut', 'Drummernaut', 'Ganger Chieftain');
        $r[] = $this->ruleHasCards($deck, 'Cosecha hasta 6 veces con Drummernaut', 'Drummernaut', 'Mega Ganger Chieftain');
        $r[] = $this->ruleHasCards($deck, 'Captura y lo manda al otro campo', 'Deusillus', 'Exile');
        $r[] = $this->ruleHasCards($deck, 'Captura, exalta y obtiene el ámbar', 'Tribute', 'Exile');
        $r[] = $this->ruleHasCards($deck, 'Captura, exalta y lo manda al otro campo', 'Tribute', 'Sic Semper Tyrannosaurus');
        $r[] = $this->ruleHasCards($deck, 'Captura y lo manda al otro campo', 'Crassosaurus', 'Sic Semper Tyrannosaurus');
        $r[] = $this->ruleHasCards($deck, 'Prepara hasta seis veces a Rex', 'Cincinnatus Rex', 'The Golden Spiral');
        $r[] = $this->ruleHasCards($deck, 'Prepara hasta seis veces a Rex', 'Cincinnatus Rex', 'Legatus Raptor');
        $r[] = $this->ruleHasCards($deck, 'Permite usar a Rex sin depender de las criaturas enemigas', 'Livia the Elder', 'Cincinnatus Rex', 'Legatus Raptor');
        $r[] = $this->ruleHasCards($deck, 'Permite usar a Rex sin depender de las criaturas enemigas', 'Livia the Elder', 'Cincinnatus Rex', 'The Golden Spiral');
        $r[] = $this->ruleHasCards($deck, 'Genera 5 de Ámbar', 'Mega Narp', 'The Flex');
        $r[] = $this->ruleHasCards($deck, 'Cosecha hasta 6 veces seguidas con el Squadron', 'Duskwitch', 'Skybooster Squadron');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Song of the Wild', 'Ghosthawk');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Song of the Wild', 'Dark Harbinger');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Commpod', 'Crystal Hive');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Combat Pheromones', 'Crystal Hive');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Hysteria', 'A Fair Game');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Hecatomb', 'Arise!');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'The Ulfberht Device', 'Allusions of Grandeur');
        $r[] = $this->ruleHasCards($deck, 'Burst', 'Loot the Bodies', 'Coward’s End');
        $r[] = $this->ruleHasCards($deck, 'Recursion de criaturas de marte + cosechas', 'Witch of the Eye', 'Brain Stem Antenna');
        $r[] = $this->ruleHasCards($deck, 'Información para hacer más efectivo el CTW', 'A Fair Game', 'Control the Weak');
        $r[] = $this->ruleHasCards($deck, 'Control y recursión del aguijón', 'The Sting', 'Whispering Reliquary');
        $r[] = $this->ruleHasCards($deck, 'Control y recursión del aguijón', 'The Sting', 'Snudge');
        $r[] = $this->ruleHasCards($deck, 'Control y recursión del aguijón', 'The Sting', 'Vezyma Thinkdrone');
        $r[] = $this->ruleHasCards($deck, 'Control y recursión del aguijón', 'The Sting', 'Barehanded');
        $r[] = $this->ruleHasCards($deck, 'Doble de beneficio', 'Obsidian Forge', 'Soul Snatcher');

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
