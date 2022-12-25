<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Fixtures\Keyforge;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagStyle;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\TagVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Persistence\Fixture;
use AdnanMula\Cards\Infrastructure\Fixtures\DbalFixture;

final class KeyforgeTagsFixtures extends DbalFixture implements Fixture
{
    public const FIXTURE_TAG_1_ID = '0d8ec28f-2fc5-4f8f-af71-7ca78efb5a66';
    public const FIXTURE_TAG_2_ID = '8d0f77a9-b5c4-4520-8ea1-e8d5187e512e';
    public const FIXTURE_TAG_3_ID = 'ee07a75f-b52d-4266-ba7f-852b1d35648f';
    public const FIXTURE_TAG_4_ID = 'd1a3dbbe-b44c-4a92-9b25-c9f811720ff2';
    public const FIXTURE_TAG_5_ID = '0c168066-0a43-45ad-87d4-0ff653370666';
    public const FIXTURE_TAG_6_ID = '5302e938-15fc-452e-8c83-a7d7d393ba2f';
    public const FIXTURE_TAG_7_ID = '3325edeb-69c7-45ce-a408-da81bbceb49f';

    private const TABLE = 'keyforge_tags';

    private bool $loaded = false;

    public function load(): void
    {
        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_1_ID),
            'Tag 1',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#8da832',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#8da832',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_2_ID),
            'Tag 2',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#42a4f5',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#42a4f5',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_3_ID),
            'Tag 3',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#8da832',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#8da832',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_4_ID),
            'Tag 4',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#d61e1e',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#d61e1e',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_5_ID),
            'Tag 5',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#8da832',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#000000',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_6_ID),
            'Tag 6',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#f1f507',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#000000',
                ],
            ),
        ));

        $this->save(new KeyforgeTag(
            Uuid::from(self::FIXTURE_TAG_7_ID),
            'Tag 7',
            TagVisibility::PUBLIC,
            TagStyle::from(
                [
                    TagStyle::COLOR_BG => '#8da832',
                    TagStyle::COLOR_TEXT => '#000000',
                    TagStyle::COLOR_OUTLINE => '#000000',
                ],
            ),
        ));

        $this->loaded = true;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function dependants(): array
    {
        return [];
    }

    private function save(KeyforgeTag $tag): void
    {
        $stmt = $this->connection->prepare(
            \sprintf(
                '
                    INSERT INTO %s (id, name, visibility, style)
                    VALUES (:id, :name, :visibility, :style)
                    ON CONFLICT (id) DO UPDATE SET
                        id = :id,
                        name = :name,
                        visibility = :visibility,
                        style = :style
                    ',
                self::TABLE,
            ),
        );

        $stmt->bindValue(':id', $tag->id->value());
        $stmt->bindValue(':name', $tag->name());
        $stmt->bindValue(':visibility', $tag->visibility()->name);
        $stmt->bindValue(':style', Json::encode($tag->style()));

        $stmt->execute();
    }
}
