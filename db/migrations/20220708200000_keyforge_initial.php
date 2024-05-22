<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class KeyforgeInitial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE keyforge_users (
                id uuid NOT NULL,
                name character varying(64) NOT NULL
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
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_data (
                id uuid NOT NULL,
                dok_id integer NOT NULL,
                sas integer NOT NULL,
                previous_sas_rating integer NOT NULL,
                previous_major_sas_rating integer NOT NULL,
                sas_percentile numeric NOT NULL,
                sas_version integer NOT NULL,
                aerc_score integer NOT NULL,
                aerc_version integer NOT NULL,
                amber_control numeric NOT NULL,
                artifact_control numeric NOT NULL,
                expected_amber numeric NOT NULL,
                creature_control numeric NOT NULL,
                efficiency numeric NOT NULL,
                recursion numeric NOT NULL,
                disruption numeric NOT NULL,
                effective_power numeric NOT NULL,
                creature_protection numeric NOT NULL,
                other numeric NOT NULL,
                raw_amber integer NOT NULL,
                total_power integer NOT NULL,
                total_Armor integer NOT NULL,
                efficiency_bonus numeric NOT NULL,
                creature_count integer NOT NULL,
                action_count integer NOT NULL,
                artifact_count integer NOT NULL,
                upgrade_count integer NOT NULL,
                card_draw_count integer NOT NULL,
                card_archive_count integer NOT NULL,
                key_cheat_count integer NOT NULL,
                synergy_rating integer NOT NULL,
                anti_synergy_rating integer NOT NULL,
                extra_data jsonb NOT NULL,
                last_sas_update TIMESTAMP WITH TIME ZONE NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_user_data (
                id uuid NOT NULL,
                wins integer NOT NULL,
                losses integer NOT NULL,
                owner uuid,
                notes character varying(512) NOT NULL,
                tags jsonb NOT NULL DEFAULT \'[]\',
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_past_sas (
                id uuid NOT NULL,
                stat_expected_amber numeric NOT NULL,
                stat_amber_control numeric NOT NULL,
                stat_creature_control numeric NOT NULL,
                stat_artifact_control numeric NOT NULL,
                stat_efficiency numeric NOT NULL,
                stat_recursion numeric NOT NULL,
                stat_creature_protection numeric NOT NULL,
                stat_disruption numeric NOT NULL,
                stat_other numeric NOT NULL,
                stat_effective_power numeric NOT NULL,
                stat_aerc_score numeric NOT NULL,
                stat_sas_rating numeric NOT NULL,
                stat_synergy_rating numeric NOT NULL,
                stat_antisynergy_rating numeric NOT NULL,
                stat_aerc_version numeric NOT NULL,
                updated_at TIMESTAMP WITH TIME ZONE NULL,
                dok_updated_at TIMESTAMP WITH TIME ZONE NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_tags (
                id uuid NOT NULL,
                name jsonb NOT NULL,
                visibility character varying(16) NOT NULL,
                style jsonb NOT NULL,
                type character varying(64) NOT NULL,
                archived boolean NOT NULL,
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
                competition character varying(64) NOT NULL,
                notes character varying(512) NOT NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_competitions (
                id uuid NOT NULL,
                reference character varying(64) NOT NULL
                   CONSTRAINT reference_unique UNIQUE,
                name character varying(64) NOT NULL
                   CONSTRAINT competition_name_unique UNIQUE,
                competition_type character varying(64) NOT NULL,
                users jsonb NOT NULL,
                description character varying(512) NOT NULL,
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
        $this->execute('DROP TABLE IF EXISTS "keyforge_tags"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_decks"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_users"');
    }
}
