<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\ValueObject;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use Assert\Assert;

final class Link extends StringValueObject
{
    public const string DECK_REGULAR = 'decksofkeyforge.com/decks/';
    public const string DECK_ALLIANCE = 'decksofkeyforge.com/alliance-decks/';
    public const string DECK_THEORETICAL = 'decksofkeyforge.com/theoretical-decks/';
    private const string HTTPS = 'https://';

    public static function from(string $value): static
    {
        Assert::that($value)->url();

        return new static($value);
    }

    public static function dokDeckFromId(KeyforgeDeckType $type, Uuid $uuid): static
    {
        $domain = match ($type) {
            KeyforgeDeckType::STANDARD => self::DECK_REGULAR,
            KeyforgeDeckType::ALLIANCE => self::DECK_ALLIANCE,
            KeyforgeDeckType::THEORETICAL => self::DECK_THEORETICAL,
        };

        return self::from(self::HTTPS . $domain . $uuid->value());
    }
}
