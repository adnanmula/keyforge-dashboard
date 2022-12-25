<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use Assert\Assert;

final class TagStyle implements \JsonSerializable
{
    public const COLOR_BG = 'color_bg';
    public const COLOR_TEXT = 'color_text';
    public const COLOR_OUTLINE = 'color_outline';

    private function __construct(
        private readonly string $colorBg,
        private readonly string $colorText,
        private readonly string $colorOutline,
    ) {}

    public static function from(array $style): TagStyle
    {
        Assert::lazy()
            ->that($style)->isArray()
            ->keyExists(self::COLOR_BG)
            ->keyExists(self::COLOR_TEXT)
            ->keyExists(self::COLOR_OUTLINE)
            ->verifyNow();

        $colorRegex = '/^#(?:[0-9a-fA-F]{3}){1,2}$/';

        Assert::lazy()
            ->that($style[self::COLOR_BG])->regex($colorRegex)
            ->that($style[self::COLOR_TEXT])->regex($colorRegex)
            ->that($style[self::COLOR_OUTLINE])->regex($colorRegex)
            ->verifyNow();

        return new static(
            $style[self::COLOR_BG],
            $style[self::COLOR_TEXT],
            $style[self::COLOR_OUTLINE],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            self::COLOR_BG => $this->colorBg,
            self::COLOR_TEXT => $this->colorText,
            self::COLOR_OUTLINE => $this->colorOutline,
        ];
    }
}
