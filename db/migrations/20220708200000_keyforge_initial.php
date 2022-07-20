<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class KeyforgeInitial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE keyforge_users (
                id uuid NOT NULL,
                name character varying(32) NOT NULL
                   CONSTRAINT keyforge_name_unique UNIQUE,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
        'CREATE TABLE keyforge_decks (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                set character varying(16) NOT NULL,
                houses jsonb NOT NULL,
                sas integer NOT NULL,
                wins integer NOT NULL,
                losses integer NOT NULL,
                extra_data jsonb NOT NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_games (
                id uuid NOT NULL,
                winner uuid NOT NULL,
                loser uuid NOT NULL,            
                winner_deck uuid NOT NULL,
                loser_deck uuid NOT NULL,
                first_turn uuid NULL,
                score jsonb NOT NULL,
                date TIMESTAMP WITH TIME ZONE NULL,
                created_at TIMESTAMP WITH TIME ZONE NULL,
                winner_chains integer NOT NULL,
                loser_chains integer NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_users"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_decks"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_games"');
    }
}
