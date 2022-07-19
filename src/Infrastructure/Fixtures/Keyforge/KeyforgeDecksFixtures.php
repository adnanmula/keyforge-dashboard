<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckHouses;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeHouse;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;
use AdnanMula\Cards\Infrastructure\Fixtures\User\UserFixtures;

final class KeyforgeDecksFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_KEYFORGE_DECK_1_ID = '10ff6ac7-c6c9-444b-a1aa-10fe87c3c524';
    public const FIXTURE_KEYFORGE_DECK_2_ID = '496b4258-b02c-4270-9918-4fd9c3366986';
    public const FIXTURE_KEYFORGE_DECK_3_ID = 'aa99749f-79b3-4040-8cd7-5c824cf3da3b';
    public const FIXTURE_KEYFORGE_DECK_4_ID = 'deb90365-d69e-4ed4-9bf9-796320230ebb';

    private const TABLE = 'keyforge_decks';

    private bool $loaded = false;

    public function load(): void
    {
        // @codingStandardsIgnoreStart
        $rawExtraData1 = '{"deck":{"id":1687443,"keyforgeId":"10ff6ac7-c6c9-444b-a1aa-10fe87c3c524","expansion":"CALL_OF_THE_ARCHONS","name":"Parker la Sedienta","creatureCount":14,"actionCount":20,"upgradeCount":2,"expectedAmber":30.000000000000004,"amberControl":9.7,"creatureControl":3.325,"efficiency":16.35,"recursion":1.21875,"effectivePower":50,"disruption":1,"aercScore":60,"previousSasRating":70,"previousMajorSasRating":69,"aercVersion":42,"sasRating":71,"synergyRating":14,"antisynergyRating":1,"metaScores":[],"efficiencyBonus":0,"totalPower":40,"cardDrawCount":4,"cardArchiveCount":2,"rawAmber":15,"lastSasUpdate":"2021-12-03","sasPercentile":86.77955322654233,"housesAndCards":[{"house":"Logos","cards":[{"cardTitle":"Dimension Door","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Interdimensional Graft","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Library Access","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Neuro Syphon","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Neuro Syphon","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Phase Shift","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Phase Shift","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Sloppy Labwork","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Wild Wormhole","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Batdrone","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Doc Bookton","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Dysania","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false}]},{"house":"Shadows","cards":[{"cardTitle":"Bait and Switch","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Ghostly Hand","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Ghostly Hand","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Hidden Stash","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Nerve Blast","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"One Last Job","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Poison Wave","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Bad Penny","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Macis Asp","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Umbra","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Urchin","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Duskrunner","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false}]},{"house":"Untamed","cards":[{"cardTitle":"Full Moon","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Regrowth","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Save the Pack","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Vigor","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Dew Faerie","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Halacor","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Hunting Witch","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Murmook","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Niffle Ape","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Snufflegator","rarity":"Common","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Witch of the Wilds","rarity":"Rare","legacy":false,"maverick":false,"anomaly":false},{"cardTitle":"Way of the Bear","rarity":"Uncommon","legacy":false,"maverick":false,"anomaly":false}]}],"dateAdded":"2020-01-21"},"sasVersion":42}';
        $rawExtraData2 = '{"deck": {"id": 16228036, "name": "Lydia la Inacabable de la Colmena", "other": 0.75, "rawAmber": 10, "aercScore": 53, "dateAdded": "2021-06-26", "expansion": "AGE_OF_ASCENSION", "recursion": 0.75, "sasRating": 63, "efficiency": 8.05, "keyforgeId": "496b4258-b02c-4270-9918-4fd9c3366986", "metaScores": [], "totalArmor": 10, "totalPower": 56, "actionCount": 9, "aercVersion": 42, "amberControl": 7.125, "upgradeCount": 2, "artifactCount": 2, "cardDrawCount": 4, "creatureCount": 23, "expectedAmber": 17.9725, "keyCheatCount": 1, "lastSasUpdate": "2021-12-03", "sasPercentile": 48.2740965578756, "synergyRating": 9, "effectivePower": 66, "housesAndCards": [{"cards": [{"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Binate Rupture"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Cutthroat Research"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Cutthroat Research"}, {"legacy": false, "rarity": "FIXED", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Help from Future Self"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Quantum Fingertrap"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Eyegor"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Eyegor"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Hexpion"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Pip Pip"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Timetraveller"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Titan Mechanic"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Rocket Boots"}], "house": "Logos"}, {"cards": [{"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Equalize"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Shield of Justice"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Take Hostages"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Haedroth’s Wall"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Aubade the Grim"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Barrister Joya"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Challe the Safeguard"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Rothais the Fierce"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Sir Marrows"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Sir Marrows"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "The Grey Rider"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Seraphic Armor"}], "house": "Sanctum"}, {"cards": [{"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Nightforge"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Oubliette"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Brend the Fanatic"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Lamindra"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Plague Rat"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Ronnie Wristclocks"}], "house": "Shadows"}], "creatureControl": 10.04, "efficiencyBonus": 0, "antisynergyRating": 0, "previousSasRating": 64, "creatureProtection": 2.5, "previousMajorSasRating": 0}, "sasVersion": 42}';
        $rawExtraData3 = '{"deck": {"id": 2144406, "name": "Harrison “Sátiro”, Rebelde del Foro", "other": 1, "rawAmber": 10, "aercScore": 60, "dateAdded": "2020-11-12", "expansion": "MASS_MUTATION", "recursion": 0.5, "sasRating": 72, "disruption": -1.5, "efficiency": 12.782499999999999, "keyforgeId": "aa99749f-79b3-4040-8cd7-5c824cf3da3b", "metaScores": [], "totalArmor": 7, "totalPower": 68, "actionCount": 14, "aercVersion": 42, "amberControl": 7.6175, "artifactCount": 4, "cardDrawCount": 2, "creatureCount": 18, "expectedAmber": 14.9375, "keyCheatCount": 1, "lastSasUpdate": "2021-12-03", "sasPercentile": 89.48404810142553, "synergyRating": 10, "effectivePower": 74, "housesAndCards": [{"cards": [{"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Gateway to Dis"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Harvest Time"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Waking Nightmare"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Waking Nightmare"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Obsidian Forge"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Dark Minion"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Drecker"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Impspector"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Lord Invidius"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Picaroon"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Snarette"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Snarette"}], "house": "Dis"}, {"cards": [{"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Causal Loop"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Diametric Charge"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Eclectic Inquiry"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Effervescent Principle"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Auto-Encoder"}, {"legacy": false, "rarity": "Rare", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "The Howling Pit"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Bot Bookton"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Chronus"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Infomorph"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Novu Dynamo"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Research Smoko"}, {"legacy": false, "rarity": "Variant", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Xeno-Bot"}], "house": "Logos"}, {"cards": [{"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Burning Glare"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Burning Glare"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Cleansing Wave"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Commandeer"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Radiant Truth"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Smite"}, {"legacy": false, "rarity": "Uncommon", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Gorm of Omm"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Champion Anaphiel"}, {"legacy": false, "rarity": "Variant", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Dino-Knight"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "Gizelhart’s Zealot"}, {"legacy": false, "rarity": "Variant", "anomaly": false, "enhanced": true, "maverick": false, "cardTitle": "Lyco-Knight"}, {"legacy": false, "rarity": "Common", "anomaly": false, "enhanced": false, "maverick": false, "cardTitle": "The Grey Rider"}], "house": "Sanctum"}], "artifactControl": 3.25, "creatureControl": 15.575, "efficiencyBonus": 0, "cardArchiveCount": 2, "antisynergyRating": 0, "previousSasRating": 71, "creatureProtection": 0.5, "previousMajorSasRating": 0}, "sasVersion": 42}';
        $rawExtraData4 = '{"deck":{"id":20199300,"name":"Cassiopeia la Artera","other":-0.25,"rawAmber":11,"aercScore":54,"dateAdded":"2022-07-07","expansion":"DARK_TIDINGS","recursion":1.25,"sasRating":61,"disruption":0.25,"efficiency":6.62,"keyforgeId":"deb90365-d69e-4ed4-9bf9-796320230ebb","metaScores":[],"totalArmor":7,"totalPower":72,"actionCount":14,"aercVersion":42,"amberControl":5.15,"upgradeCount":2,"artifactCount":4,"cardDrawCount":1,"creatureCount":16,"expectedAmber":22.5125,"lastSasUpdate":"2022-07-07","sasPercentile":36.40633733493926,"synergyRating":8,"effectivePower":94,"housesAndCards":[{"cards":[{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Cleansing Wave"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"First or Last"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Hammer-gram"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Heal or Harm"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Light Everlasting"},{"legacy":false,"rarity":"Rare","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Round Table"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Badgemagus"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Bull-wark"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Gatekeeper"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Gatekeeper"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Orphiel, Sea\'s Chosen"},{"legacy":false,"rarity":"FIXED","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Orphion, Land\'s Chosen"}],"house":"Sanctum"},{"cards":[{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Carpe Vinum"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Decadence"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Hedonistic Intent"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Reach Advantage"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Spoils of Battle"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Spoils of Battle"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Altruist\'s Rostrum"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Saurarium"},{"legacy":false,"rarity":"FIXED","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Charybdis"},{"legacy":false,"rarity":"Rare","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Scylla"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Senator Quintina"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Venator Altum"}],"house":"Saurian"},{"cards":[{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Lay of the Land"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Technobabble"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Unity or Discord"},{"legacy":false,"rarity":"Rare","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Book of leQ"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"CXO Taber"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Diplomat Agung"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Lieutenant Khrkhar"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Lieutenant Khrkhar"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Operative Espion"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Shield-U-Later"},{"legacy":false,"rarity":"Uncommon","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Force Field"},{"legacy":false,"rarity":"Common","anomaly":false,"enhanced":false,"maverick":false,"cardTitle":"Light of the Archons"}],"house":"StarAlliance"}],"creatureControl":7.425,"efficiencyBonus":0,"antisynergyRating":1,"previousSasRating":0,"creatureProtection":2.6375,"previousMajorSasRating":0},"sasVersion":42}';
        // @codingStandardsIgnoreEnd

        $extraData1 = \json_decode($rawExtraData1, true, 512, \JSON_THROW_ON_ERROR);

        $this->save(
            new KeyforgeDeck(
                Uuid::from(self::FIXTURE_KEYFORGE_DECK_1_ID),
                'Parker la Sedienta',
                KeyforgeSet::CotA,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::UNTAMED,
                    KeyforgeHouse::SHADOWS,
                    KeyforgeHouse::LOGOS,
                ),
                71,
                1,
                1,
                $extraData1,
            ),
        );

        $extraData2 = \json_decode($rawExtraData2, true, 512, \JSON_THROW_ON_ERROR);

        $this->save(
            new KeyforgeDeck(
                Uuid::from(self::FIXTURE_KEYFORGE_DECK_2_ID),
                'Lydia la Inacabable de la Colmena',
                KeyforgeSet::AoA,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::LOGOS,
                    KeyforgeHouse::SHADOWS,
                    KeyforgeHouse::SANCTUM,
                ),
                63,
                0,
                2,
                $extraData2,
            ),
        );

        $extraData3 = \json_decode($rawExtraData3, true, 512, \JSON_THROW_ON_ERROR);

        $this->save(
            new KeyforgeDeck(
                Uuid::from(self::FIXTURE_KEYFORGE_DECK_3_ID),
                'Harrison "Sátiro", Rebelde del Foro',
                KeyforgeSet::MM,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::DIS,
                    KeyforgeHouse::LOGOS,
                    KeyforgeHouse::SANCTUM,
                ),
                72,
                1,
                0,
                $extraData3,
            ),
        );

        $extraData4 = \json_decode($rawExtraData4, true, 512, \JSON_THROW_ON_ERROR);

        $this->save(
            new KeyforgeDeck(
                Uuid::from(self::FIXTURE_KEYFORGE_DECK_4_ID),
                'Cassiopeia la Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'C2assiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );

        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );
        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );
        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );
        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );
        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );
        $this->save(
            new KeyforgeDeck(
                Uuid::v4(),
                'Cassiopeia la 2Artera',
                KeyforgeSet::DT,
                KeyforgeDeckHouses::from(
                    KeyforgeHouse::SANCTUM,
                    KeyforgeHouse::SAURIAN,
                    KeyforgeHouse::STAR_ALLIANCE,
                ),
                61,
                1,
                0,
                $extraData4,
            ),
        );

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    private function save(KeyforgeDeck $deck): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, set, houses, sas, wins, losses, extra_data)
                    VALUES (:id, :name, :set, :houses, :sas, :wins, :losses, :extra_data)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        set = :set,
                        houses = :houses,
                        sas = :sas,
                        wins = :wins,
                        losses = :losses,
                        extra_data = :extra_data
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $deck->id()->value());
        $stmt->bindValue(':name', $deck->name());
        $stmt->bindValue(':set', $deck->set()->name);
        $stmt->bindValue(':houses', \json_encode($deck->houses()->value()));
        $stmt->bindValue(':sas', $deck->sas());
        $stmt->bindValue(':wins', $deck->wins());
        $stmt->bindValue(':losses', $deck->losses());
        $stmt->bindValue(':extra_data', \json_encode($deck->extraData()));

        $stmt->execute();
    }
}
