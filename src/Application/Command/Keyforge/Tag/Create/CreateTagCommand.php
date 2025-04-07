<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Create;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Shared\LocalizedString;
use Assert\Assert;

final readonly class CreateTagCommand
{
    private(set) Uuid $id;
    private(set) TagVisibility $visibility;
    private(set) LocalizedString $name;
    private(set) TagStyle $style;
    private(set) TagType $type;
    private(set) ?Uuid $deckId;

    public function __construct($id, $name, $visibility, $type, $styleBg, $styleText, $styleOutline, $deckId)
    {
        Assert::lazy()
            ->that($id, 'id')->uuid()
            ->that($name, 'name')->string()->notBlank()->maxLength(100)
            ->that($visibility, 'visibility')->inArray(TagVisibility::values())
            ->that($type, 'type')->inArray(TagType::values())
            ->that($styleBg, 'styleBg')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->that($styleText, 'styleText')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->that($styleOutline, 'styleOutline')->string()->regex('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/')
            ->that($deckId, 'deckId')->nullOr()->uuid()
            ->verifyNow();

        $this->id = Uuid::from($id);
        $this->name = LocalizedString::fromArray([
            Locale::es_ES->value => $name,
            Locale::en_GB->value => $name,
        ]);
        $this->visibility = TagVisibility::from($visibility);
        $this->type = TagType::from($type);
        $this->style = TagStyle::from([
            TagStyle::COLOR_BG => $styleBg,
            TagStyle::COLOR_TEXT => $styleText,
            TagStyle::COLOR_OUTLINE => $styleOutline,
        ]);
        $this->deckId = Uuid::fromNullable($deckId);
    }
}
