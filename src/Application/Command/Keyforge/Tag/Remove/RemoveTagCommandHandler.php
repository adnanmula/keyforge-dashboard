<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Remove;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class RemoveTagCommandHandler
{
    public function __construct(
        private KeyforgeTagRepository $tagRepository,
        private KeyforgeDeckRepository $deckRepository,
        private Security $security,
    ) {}

    public function __invoke(RemoveTagCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Forbidden');
        }

        $tag = $this->tagRepository->searchOne(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringFilterValue($command->id->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('user_id'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
            ),
        ));

        if (null === $tag) {
            throw new \Exception('Forbidden');
        }

        $this->tagRepository->remove($tag->id);
        $this->removeTagFromDecks($user, $command);
    }

    private function removeTagFromDecks(?User $user, RemoveTagCommand $command): void
    {
        $decksOwned = $this->deckRepository->ownedBy($user->id());

        foreach ($decksOwned as $deckOwned) {
            $currentTags = Json::decode($deckOwned['user_tags']);
            $newTags = \array_values(\array_unique(\array_filter($currentTags, static fn (string $tag) => $tag !== $command->id->value())));

            $this->deckRepository->updateUserTags($user->id(), Uuid::from($deckOwned['deck_id']), ...$newTags);
        }
    }
}
