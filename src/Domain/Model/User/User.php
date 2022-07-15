<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class User
{
    private const MODEL_NAME = 'user';

    private function __construct(
        private Uuid $id,
        private string $name
    ) {}

    public static function create(Uuid $id, string $name): self
    {
        return new self($id, $name);
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public static function modelName(): string
    {
        return self::MODEL_NAME;
    }
}
