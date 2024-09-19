<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Alliance extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('ALTER TABLE keyforge_decks ADD deck_type character varying(20) NULL;');
        $this->execute("UPDATE keyforge_decks SET deck_type = 'STANDARD';");
        $this->execute('ALTER TABLE keyforge_decks ALTER deck_type DROP DEFAULT, ALTER deck_type SET NOT NULL;');
        $this->execute('ALTER TABLE keyforge_decks ADD alliance_composition jsonb NULL;');
    }

    public function down(): void
    {
        $this->execute('ALTER TABLE keyforge_decks DROP deck_type;');
        $this->execute('ALTER TABLE keyforge_decks DROP alliance_composition;');
    }
}
