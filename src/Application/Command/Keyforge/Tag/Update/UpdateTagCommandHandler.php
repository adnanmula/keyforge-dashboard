<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Tag\Update;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckTag;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeTagRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UpdateTagCommandHandler
{
    public function __construct(
        private KeyforgeTagRepository $repository,
        private Security $security,
    ) {}

    public function __invoke(UpdateTagCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Forbidden');
        }

        $tag = $this->repository->searchOne(new Criteria(
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringFilterValue($command->id->value()), FilterOperator::EQUAL),
            ),
        ));

        $newTag = new KeyforgeDeckTag(
            $tag->id,
            $command->name,
            $tag->visibility,
            $command->style,
            $tag->type,
            $tag->archived,
        );

        $this->repository->save($newTag);
    }
}
