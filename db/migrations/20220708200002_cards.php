<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Cards extends AbstractMigration
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
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_cards"');
    }
}
