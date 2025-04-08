<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Update;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;
use Assert\Assert;

final readonly class UpdateTagCommand
{
    private(set) Uuid $id;
    private(set) LocalizedString $name;
    private(set) TagStyle $style;

    public function __construct($id, $name, $styleBg, $styleText, $styleOutline)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($name, 'name')->string()->notBlank()->maxLength(100)
            ->that($styleBg, 'styleBg')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->that($styleText, 'styleText')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->that($styleOutline, 'styleOutline')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->verifyNow();

        $this->id = Uuid::from($id);
        $this->name = LocalizedString::fromArray([
            Locale::es_ES->value => $name,
            Locale::en_GB->value => $name,
        ]);
        $this->style = TagStyle::from([
            TagStyle::COLOR_BG => $styleBg,
            TagStyle::COLOR_TEXT => $styleText,
            TagStyle::COLOR_OUTLINE => $styleOutline,
        ]);
    }
}
