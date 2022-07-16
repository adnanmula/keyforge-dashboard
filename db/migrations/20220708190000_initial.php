<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Initial extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE users (
                id uuid NOT NULL,
                name character varying(32) NOT NULL
                   CONSTRAINT name_unique UNIQUE,
                password character varying(64) NOT NULL,
                roles jsonb NOT NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "users"');
    }
}
