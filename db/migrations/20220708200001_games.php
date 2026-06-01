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
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_game_logs (
                id uuid NOT NULL,
                game_id uuid NULL,
                log jsonb NOT NULL,
                created_by character varying(64) NULL,
                created_at TIMESTAMP WITH TIME ZONE NOT NULL,
                turns INTEGER NULL,
                winner_amber_obtained INTEGER NULL,
                winner_amber_stolen INTEGER NULL,
                winner_cards_played INTEGER NULL,
                winner_cards_drawn INTEGER NULL,
                winner_cards_discarded INTEGER NULL,
                winner_keys_forged INTEGER NULL,
                winner_fights INTEGER NULL,
                winner_reaps INTEGER NULL,
                winner_extra_turns INTEGER NULL,
                loser_amber_obtained INTEGER NULL,
                loser_amber_stolen INTEGER NULL,
                loser_cards_played INTEGER NULL,
                loser_cards_drawn INTEGER NULL,
                loser_cards_discarded INTEGER NULL,
                loser_keys_forged INTEGER NULL,
                loser_fights INTEGER NULL,
                loser_reaps INTEGER NULL,
                loser_extra_turns INTEGER NULL,
                total_amber_obtained INTEGER NULL,
                total_amber_stolen INTEGER NULL,
                total_cards_played INTEGER NULL,
                total_cards_drawn INTEGER NULL,
                total_cards_discarded INTEGER NULL,
                total_keys_forged INTEGER NULL,
                total_fights INTEGER NULL,
                total_reaps INTEGER NULL,
                total_extra_turns INTEGER NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_competitions (
                id uuid NOT NULL,
                name character varying(64) NOT NULL CONSTRAINT competition_name_unique UNIQUE,
                competition_type character varying(64) NOT NULL,
                fixtures_type character varying(64) NOT NULL,
                admins jsonb NOT NULL,
                players jsonb NOT NULL,
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
                players jsonb NOT NULL,
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
        $this->execute('DROP TABLE IF EXISTS "keyforge_game_logs"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_games"');
    }
}
