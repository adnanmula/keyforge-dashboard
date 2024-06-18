<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Users extends AbstractMigration
{
    public function up(): void
    {
        $this->execute(
        'CREATE TABLE users (
                id uuid NOT NULL,
                name character varying(32) NOT NULL
                   CONSTRAINT name_unique UNIQUE,
                password character varying(64) NOT NULL,
                locale character varying(5) NOT NULL,
                roles jsonb NOT NULL,
                PRIMARY KEY (id)
            )',
        );

        $this->execute(
            'CREATE TABLE user_friends (
                id uuid NOT NULL,
                friend_id uuid NOT NULL,
                is_request bool NOT NULL,
                PRIMARY KEY (id, friend_id)
            )',
        );

        $this->execute('CREATE INDEX ON user_friends (id);');
        $this->execute('CREATE INDEX ON user_friends (friend_id);');

        $this->execute(
            'CREATE TABLE keyforge_users (
                id uuid NOT NULL,
                name character varying(64) NOT NULL,
                owner uuid NULL,
                PRIMARY KEY(id)
            )',
        );
    }

    public function down(): void
    {
        $this->execute('DROP TABLE IF EXISTS "users"');
        $this->execute('DROP TABLE IF EXISTS "user_friends"');
        $this->execute('DROP TABLE IF EXISTS "keyforge_users"');
    }
}
