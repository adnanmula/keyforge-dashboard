<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use Assert\Assert;

final class TagStyle implements \JsonSerializable
{
    private function __construct(
        private readonly string $color,
    ) {}

    public static function from(array $style): TagStyle
    {
        Assert::lazy()
            ->that($style)->isArray()
            ->keyExists('color')
            ->verifyNow();

        Assert::lazy()
            ->that($style['color'])->regex('/^#(?:[0-9a-fA-F]{3}){1,2}$/')
            ->verifyNow();

        return new static($style['color']);
    }

    public function jsonSerialize(): array
    {
        return [
            'color' => $this->color,
        ];
    }
}
