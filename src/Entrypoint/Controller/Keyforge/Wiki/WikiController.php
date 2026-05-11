<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Wiki;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Response;

final class WikiController extends Controller
{
    public function __invoke(): Response
    {
        $housesPerSet = [];
        foreach (KeyforgeSet::cases() as $set) {
            if ($set->isEnabled()) {
                foreach (KeyforgeHouse::cases() as $house) {
                    if ($house->isEnabled()) {
                        $housesPerSet[$set->value][$house->value] = false;
                    }
                }
            }
        }

        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::CotA->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::AoA->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::WC->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;

        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::MM->value][KeyforgeHouse::SANCTUM->value] = true;

        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::DT->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::WoE->value][KeyforgeHouse::EKWIDON->value] = true;

        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::EKWIDON->value] = true;
        $housesPerSet[KeyforgeSet::GR->value][KeyforgeHouse::GEISTOID->value] = true;

        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::EKWIDON->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::GEISTOID->value] = true;
        $housesPerSet[KeyforgeSet::AS->value][KeyforgeHouse::SKYBORN->value] = true;

        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::PV->value][KeyforgeHouse::REDEMPTION->value] = true;

        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::EKWIDON->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::GEISTOID->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::SKYBORN->value] = true;
        $housesPerSet[KeyforgeSet::DM->value][KeyforgeHouse::OUBOROS->value] = true;

        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::U22->value][KeyforgeHouse::EKWIDON->value] = true;

        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::VM23->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::VM24->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::UNFATHOMABLE->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::EKWIDON->value] = true;
        $housesPerSet[KeyforgeSet::VM25->value][KeyforgeHouse::GEISTOID->value] = true;

        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::MoM->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::REDEMPTION->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::GEISTOID->value] = true;
        $housesPerSet[KeyforgeSet::ToC->value][KeyforgeHouse::SKYBORN->value] = true;

        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::SANCTUM->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::SHADOWS->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::DIS->value][KeyforgeHouse::UNTAMED->value] = true;

        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::SKYBORN->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::UNTAMED->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::CC->value][KeyforgeHouse::SANCTUM->value] = true;

        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::GEISTOID->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::LOGOS->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::MARS->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::STAR_ALLIANCE->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::KEYRAKEN->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::SAURIAN->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::DIS->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::EKWIDON->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::BROBNAR->value] = true;
        $housesPerSet[KeyforgeSet::M24->value][KeyforgeHouse::UNFATHOMABLE->value] = true;

        return $this->render('Keyforge/Wiki/wiki.html.twig', ['housesPerSet' => $housesPerSet]);
    }
}
