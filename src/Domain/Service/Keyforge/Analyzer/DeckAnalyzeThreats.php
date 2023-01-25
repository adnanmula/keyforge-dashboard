<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Keyforge\Analyzer;

enum DeckAnalyzeThreats: string
{
    case CAN_DAMAGE_IF_NEIGHBORS = 'Puede hacer mucho daño si vecinos comparten casa';
    case CAN_LIFT_CREATURES = 'Puede levantar criaturas en flancos';
    case CAN_BENEFIT_FROM_AREA_DAMAGE = 'Puede aprovechar el daño en area';
}
