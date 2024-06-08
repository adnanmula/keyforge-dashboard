<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Cards extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE keyforge_cards (
                id int NOT NULL,
                houses jsonb NOT NULL,
                name jsonb NOT NULL,
                name_url character varying(64) NOT NULL,
                flavor_text jsonb NULL,
                text jsonb NOT NULL,
                type character varying(32) NOT NULL,
                traits jsonb NOT NULL,
                amber integer NOT NULL,
                power integer NULL,
                armor integer NULL,
                is_big bool NOT NULL,
                is_token bool NOT NULL,
                sets jsonb NOT NULL,
                tags jsonb NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_cards"');
    }
}
