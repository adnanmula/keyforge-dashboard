<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UuidValueObject;

final class User
{
    private const MODEL_NAME = 'user';

    private function __construct(
        private UuidValueObject $id,
        private string $name
    ) {}

    public static function create(UuidValueObject $id, string $name): self
    {
        return new self($id, $name);
    }

    public function id(): UuidValueObject
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
