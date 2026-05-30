<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class GameLogTotalStats extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('
            ALTER TABLE keyforge_game_logs
                ADD COLUMN total_amber_obtained  INTEGER NULL,
                ADD COLUMN total_amber_stolen    INTEGER NULL,
                ADD COLUMN total_cards_played    INTEGER NULL,
                ADD COLUMN total_cards_drawn     INTEGER NULL,
                ADD COLUMN total_cards_discarded INTEGER NULL,
                ADD COLUMN total_keys_forged     INTEGER NULL,
                ADD COLUMN total_fights          INTEGER NULL,
                ADD COLUMN total_reaps           INTEGER NULL,
                ADD COLUMN total_extra_turns     INTEGER NULL
        ');
    }

    public function down(): void
    {
        $this->execute('
            ALTER TABLE keyforge_game_logs
                DROP COLUMN IF EXISTS total_amber_obtained,
                DROP COLUMN IF EXISTS total_amber_stolen,
                DROP COLUMN IF EXISTS total_cards_played,
                DROP COLUMN IF EXISTS total_cards_drawn,
                DROP COLUMN IF EXISTS total_cards_discarded,
                DROP COLUMN IF EXISTS total_keys_forged,
                DROP COLUMN IF EXISTS total_fights,
                DROP COLUMN IF EXISTS total_reaps,
                DROP COLUMN IF EXISTS total_extra_turns
        ');
    }
}
