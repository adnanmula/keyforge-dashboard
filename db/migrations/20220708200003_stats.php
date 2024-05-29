<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Stats extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE keyforge_stats (
                id uuid NOT NULL,
                category character varying(64) NOT NULL,
                reference uuid NULL,
                data jsonb NOT NULL,
                PRIMARY KEY(id)
            )',
        );

        $this->execute(
            'CREATE TABLE keyforge_stats_projection_pending (
                id uuid NOT NULL,
                category character varying(64) NOT NULL,
                reference uuid NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "keyforge_stats"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_stats_projection_pending"');
    }
}
