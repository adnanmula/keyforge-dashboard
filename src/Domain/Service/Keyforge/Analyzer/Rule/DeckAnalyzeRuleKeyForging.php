<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\Rule;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeRule;

final class DeckAnalyzeRuleKeyForging implements DeckAnalyzeRule
{
    use DeckAnalyzeRuleHelper;

    public const CATEGORY = 'Combo';
    public const SUBCATEGORY = 'Forja de llaves';

    private KeyforgeDeck $deck;

    public function execute(KeyforgeDeck $deck): ?array
    {
        $this->deck = $deck;

        $r = [];

        $r[] = $this->ruleHasCards('GENKA', 'Martian Generosity', 'Key Abduction');
        $r[] = $this->ruleHasCards('Llave potencialmente gratis', 'Battle Fleet', 'Key Abduction');
        $r[] = $this->ruleHasCards('Llave potencialmente gratis', 'Timequake', 'Data Forge');
        $r[] = $this->ruleHasCards('Llave potencialmente gratis', 'Final Analysis', 'Data Forge');
        $r[] = $this->ruleHasCards('Forja con Corazón activo', 'Heart of the Forest', 'Grasping Vines', 'Key Charge');
        $r[] = $this->ruleHasCards('Forja con Corazón activo', 'Heart of the Forest', 'Grasping Vines', 'Chota Hazri');
        $r[] = $this->ruleHasCards('Forja con Corazón activo', 'Heart of the Forest', 'Grasping Vines', 'Keyfrog');
        $r[] = $this->ruleHasCards('Trigger Keyfrog el mismo turno que lo juegas', 'Fangtooth Cavern', 'Keyfrog');

        $hasTheSting = $deck->data()->cards->has('The Sting');
        $hasKeyCheat = ($deck->data()->rawData['deck']['keyCheatCount'] ?? 0) > 0;

        if ($hasTheSting && $hasKeyCheat) {
            $card1 = $deck->data()->cards->get('The Sting');

            $r[] = [
                'description' => 'Forja con el aguijón activo',
                'cards' => [
                    $card1->name => $card1->serializedName,
                    'Cualquier key cheat' => null,
                ],
            ];
        }


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
