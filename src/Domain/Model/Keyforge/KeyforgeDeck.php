<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge;

use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckData;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckUserData;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;

final class KeyforgeDeck implements \JsonSerializable
{
    public function __construct(
        private readonly Uuid $id,
        private KeyforgeDeckData $data,
        private KeyforgeDeckUserData $userData,
    ) {
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function data(): KeyforgeDeckData
    {
        return $this->data;
    }

    public function setData(KeyforgeDeckData $data): void
    {
        $this->data = $data;
    }

    public function userData(): KeyforgeDeckUserData
    {
        return $this->userData;
    }

    public function setUserData(KeyforgeDeckUserData $userData): void
    {
        $this->userData = $userData;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id()->value(),
            'data' => $this->data->jsonSerialize(),
            'userData' => $this->userData()->jsonSerialize(),
        ];
    }
}
