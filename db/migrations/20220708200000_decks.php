<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Decks extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE keyforge_decks (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                set character varying(16) NOT NULL,
                houses jsonb NOT NULL,
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
                board_clear_count integer NOT NULL,
                board_clear_cards jsonb NOT NULL,
                scaling_amber_control_count integer NOT NULL,
                scaling_amber_control_cards jsonb NOT NULL,
                synergy_rating integer NOT NULL,
                anti_synergy_rating integer NOT NULL,
                last_sas_update TIMESTAMP WITH TIME ZONE NULL,
                cards jsonb NOT NULL,
                tags jsonb NOT NULL DEFAULT \'[]\',
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_user_data (
                deck_id uuid NOT NULL,
                user_id uuid NOT NULL,
                wins integer NOT NULL,
                losses integer NOT NULL,
                wins_vs_friends integer NOT NULL,
                losses_vs_friends integer NOT NULL,
                wins_vs_users integer NOT NULL,
                losses_vs_users integer NOT NULL,
                PRIMARY KEY(deck_id, user_id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_ownership (
                deck_id uuid NOT NULL,
                user_id uuid NOT NULL,
                notes character varying(512) NOT NULL,
                user_tags jsonb NOT NULL DEFAULT \'[]\',
                PRIMARY KEY(deck_id, user_id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_decks_data_history (
                dok_reference integer NOT NULL,
                deck_id uuid NOT NULL,
                dok_deck_id integer NOT NULL,
                sas integer NOT NULL,
                aerc_score integer NOT NULL,
                aerc_version integer NOT NULL,
                expected_amber numeric NOT NULL,
                amber_control numeric NOT NULL,
                creature_control numeric NOT NULL,
                artifact_control numeric NOT NULL,
                efficiency numeric NOT NULL,
                recursion numeric NOT NULL,
                creature_protection numeric NOT NULL,
                disruption numeric NOT NULL,
                other numeric NOT NULL,
                effective_power numeric NOT NULL,
                synergy_rating integer NOT NULL,
                antisynergy_rating integer NOT NULL,
                updated_at TIMESTAMP WITH TIME ZONE NULL,
                PRIMARY KEY(dok_reference)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_tags (
                id uuid NOT NULL,
                user_id uuid NULL,
                name jsonb NOT NULL,
                visibility character varying(16) NOT NULL,
                style jsonb NOT NULL,
                type character varying(64) NOT NULL,
                archived boolean NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_tags"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_decks_data_history"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_decks_user_data"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_decks"');
    }
}
