<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Games extends AbstractMigration
{
    public function up(): void
    {
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
                competition character varying(64) NOT NULL,
                notes character varying(512) NOT NULL,
                approved bool NOT NULL,
                created_by uuid NULL,
                log jsonb NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_competitions (
                id uuid NOT NULL,
                name character varying(64) NOT NULL CONSTRAINT competition_name_unique UNIQUE,
                competition_type character varying(64) NOT NULL,
                admins jsonb NOT NULL,
                users jsonb NOT NULL,
                description character varying(512) NOT NULL,
                visibility character varying(12) NOT NULL,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL,
                started_at TIMESTAMP WITH TIME ZONE NULL,
                finished_at TIMESTAMP WITH TIME ZONE NULL,
                winner uuid NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_competition_fixtures (
                id uuid NOT NULL,
                competition_id uuid NOT NULL,
                reference character varying(64) NOT NULL,
                users jsonb NOT NULL,
                fixture_type character varying(64) NOT NULL,
                position integer NOT NULL,
                created_at TIMESTAMP WITH TIME ZONE NULL,
                played_at TIMESTAMP WITH TIME ZONE NULL,
                winner uuid NULL,
                games jsonb NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_competition_fixtures"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_competitions"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_games"');
    }
}
