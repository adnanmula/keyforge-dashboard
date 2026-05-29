<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GameLogStats extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('
            ALTER TABLE keyforge_game_logs
                ADD COLUMN turns                  INTEGER NULL,
                ADD COLUMN winner_amber_obtained  INTEGER NULL,
                ADD COLUMN winner_amber_stolen    INTEGER NULL,
                ADD COLUMN winner_cards_played    INTEGER NULL,
                ADD COLUMN winner_cards_drawn     INTEGER NULL,
                ADD COLUMN winner_cards_discarded INTEGER NULL,
                ADD COLUMN winner_keys_forged     INTEGER NULL,
                ADD COLUMN winner_fights          INTEGER NULL,
                ADD COLUMN winner_reaps           INTEGER NULL,
                ADD COLUMN winner_extra_turns     INTEGER NULL,
                ADD COLUMN loser_amber_obtained   INTEGER NULL,
                ADD COLUMN loser_amber_stolen     INTEGER NULL,
                ADD COLUMN loser_cards_played     INTEGER NULL,
                ADD COLUMN loser_cards_drawn      INTEGER NULL,
                ADD COLUMN loser_cards_discarded  INTEGER NULL,
                ADD COLUMN loser_keys_forged      INTEGER NULL,
                ADD COLUMN loser_fights           INTEGER NULL,
                ADD COLUMN loser_reaps            INTEGER NULL,
                ADD COLUMN loser_extra_turns      INTEGER NULL
        ');
    }

    public function down(): void
    {
        $this->execute('
            ALTER TABLE keyforge_game_logs
                DROP COLUMN IF EXISTS turns,
                DROP COLUMN IF EXISTS winner_amber_obtained,
                DROP COLUMN IF EXISTS winner_amber_stolen,
                DROP COLUMN IF EXISTS winner_cards_played,
                DROP COLUMN IF EXISTS winner_cards_drawn,
                DROP COLUMN IF EXISTS winner_cards_discarded,
                DROP COLUMN IF EXISTS winner_keys_forged,
                DROP COLUMN IF EXISTS winner_fights,
                DROP COLUMN IF EXISTS winner_reaps,
                DROP COLUMN IF EXISTS winner_extra_turns,
                DROP COLUMN IF EXISTS loser_amber_obtained,
                DROP COLUMN IF EXISTS loser_amber_stolen,
                DROP COLUMN IF EXISTS loser_cards_played,
                DROP COLUMN IF EXISTS loser_cards_drawn,
                DROP COLUMN IF EXISTS loser_cards_discarded,
                DROP COLUMN IF EXISTS loser_keys_forged,
                DROP COLUMN IF EXISTS loser_fights,
                DROP COLUMN IF EXISTS loser_reaps,
                DROP COLUMN IF EXISTS loser_extra_turns
        ');
    }
}
