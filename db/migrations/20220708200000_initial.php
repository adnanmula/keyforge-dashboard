<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Initial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
            'CREATE TABLE users (
                    id uuid NOT NULL,
                    reference character varying(16) NOT NULL
                        CONSTRAINT reference_unique UNIQUE,
                    username character varying(32) NOT NULL,
                    PRIMARY KEY(id)
                )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "users"');
    }
}
