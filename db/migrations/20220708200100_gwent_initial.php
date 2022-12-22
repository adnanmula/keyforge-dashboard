<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GwentInitial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE gwent_users (
                id uuid NOT NULL,
                name character varying(32) NOT NULL
                   CONSTRAINT gwent_name_unique UNIQUE,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
        'CREATE TABLE gwent_decks (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                faction character varying(16) NOT NULL,
                archetype uuid,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE gwent_deck_archetypes (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                faction character varying(16) NOT NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE gwent_games (
                id uuid NOT NULL,
                user_id uuid NOT NULL,
                win boolean NOT NULL,
                opponent character varying(24),
                deck uuid NOT NULL,
                opponent_deck_archetype uuid NOT NULL,
                coin character varying(4) NULL,
                rank integer not null,
                score jsonb NOT NULL,
                date TIMESTAMP WITH TIME ZONE NULL,
                created_at TIMESTAMP WITH TIME ZONE NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "gwent_games"');
        $this->execute('DROP TABLE IF EXISTS "gwent_decks"');
        $this->execute('DROP TABLE IF EXISTS "gwent_archetypes"');
        $this->execute('DROP TABLE IF EXISTS "gwent_users"');
    }
}
