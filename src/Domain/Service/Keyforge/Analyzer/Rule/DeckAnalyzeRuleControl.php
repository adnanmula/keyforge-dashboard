<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleControl implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const string CATEGORY = 'Combo';
    public const string SUBCATEGORY = 'Control';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('Archimedes triggers', 'Bouncing Deathquark', 'Archimedes');
        $r[] = $this->ruleHasCards('Puede cosechar 6 veces con Shark', 'Neutron Shark', 'Self-Bolstering Automata');
        $r[] = $this->ruleHasCards('Puede cosechar 6 veces con Shark', 'Neutron Shark', 'Reassembling Automaton');
        $r[] = $this->ruleHasCards('Impide que la Orden se autodestruya', 'General Order 24', 'Self-Bolstering Automata');
        $r[] = $this->ruleHasCards('Impide que la Orden se autodestruya', 'General Order 24', 'Reassembling Automaton');
        $r[] = $this->ruleHasCards('Limpia de tablero', 'They’re Everywhere!', 'Save the Pack');
        $r[] = $this->ruleHasCards('Evita perder el efecto de mejoras', 'Reassembling Automaton', 'Discombobulator');
        $r[] = $this->ruleHasCards('Evita perder el efecto de mejoras', 'Reassembling Automaton', 'Quadracorder');
        $r[] = $this->ruleHasCards('Peleas ilimitadas', 'Into the Fray', 'Potion of Invulnerability');
        $r[] = $this->ruleHasCards('Puede cosechar 6 veces con Individus', 'Lord Invidius', 'Essence Scale');
        $r[] = $this->ruleHasCards('Peleas extra', 'One Stood Against Many', 'Wrath');
        $r[] = $this->ruleHasCards('Usa el archivo sin riesgo a no poder hacer la abilidad de Gravitron', 'Ultra Gravitron', 'Causal Loop');
        $r[] = $this->ruleHasCards('Resuelve primero el Guardaalmas de forma que no se pierda', 'Reassembling Automaton', 'Soulkeeper');
        $r[] = $this->ruleHasCards('Inutiliza el campro rival un turno', "Rakuzel's Chant", 'Storm Surge');

        $hasTheSting = $deck->cards()->has('The Sting');
        $hasKeyCheat = ($deck->stats()->keyCheatCount ?? 0) > 0;

        if ($hasTheSting && $hasKeyCheat) {
            $card1 = $deck->cards()->get('The Sting');

            if (null !== $card1) {
                $r[] = [
                    'description' => 'Forja con el aguijón activo',
                    'cards' => [
                        $card1->name => $card1->serializedName,
                        'Cualquier key cheat' => null,
                    ],
                ];
            }
        }

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
