<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Create;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CreateTagCommandHandler
{
    public function __construct(
        private KeyforgeTagRepository $tagRepository,
        private KeyforgeDeckRepository $deckRepository,
        private Security $security,
    ) {}

    public function __invoke(CreateTagCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Forbidden');
        }

        $tag = new KeyforgeDeckTag(
            $command->id,
            $command->name,
            $command->visibility,
            $command->style,
            $command->type,
            false,
            $user->id(),
        );

        $this->tagRepository->save($tag);

        if (null !== $command->deckId) {
            $this->addTagToDeck($command->id, $command->deckId, $user);
        }
    }

    private function addTagToDeck(Uuid $id, Uuid $deckId, User $user): void
    {
        $deck = $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('id'), new StringFilterValue($deckId->value()), FilterOperator::EQUAL),
                ),
            ),
        );

        if (null === $deck) {
            return;
        }

        $isOwner = \count(\array_filter(
            $this->deckRepository->ownedBy($user->id()),
            static fn (array $deckOwnership): bool => $deckOwnership['user_id'] === $user->id()->value(),
        )) > 0;

        if (false === $isOwner) {
            return;
        }

        $deck->setTags(...\array_merge($deck->tags(), [$id->value()]));
        $this->deckRepository->save($deck);
    }
}
