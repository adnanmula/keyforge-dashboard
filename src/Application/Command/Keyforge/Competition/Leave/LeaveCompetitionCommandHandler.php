<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Leave;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class LeaveCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private UserRepository $userRepository,
        private TranslatorInterface $translator,
        private Security $security,
    ) {}

    public function __invoke(LeaveCompetitionCommand $command): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new AccessDeniedException();
        }

        $competition = $this->repository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(
                        new FilterField('id'),
                        new StringFilterValue($command->id->value()),
                        FilterOperator::EQUAL,
                    ),
                ),
            ),
        );


        $this->assert($user, $competition);

        if (false === \in_array($user->id()->value(), $competition->players, true)) {
            $competition->updatePlayers(...\array_merge(
                $competition->players,
                [$user->id()->value()],
            ));

            $this->repository->save($competition);
        }
    }

    private function assert(User $user, ?KeyforgeCompetition $competition): void
    {
        if (null === $competition) {
            throw new \Exception($this->translator->trans('competition.error.not_found'));
        }

        if (null !== $competition->startedAt) {
            throw new \Exception($this->translator->trans('competition.error.already_started'));
        }

        if (null !== $competition->finishedAt) {
            throw new \Exception($this->translator->trans('competition.error.already_finished'));
        }

        if ($competition->visibility === CompetitionVisibility::FRIENDS) {
            $friends = [];

            foreach ($competition->admins as $admin) {
                $friends = \array_merge(
                    $friends,
                    \array_map(
                        static fn (array $u) => $u['id'],
                        $this->userRepository->friends($admin),
                    ),
                );
            }

            if (false === \in_array($user->id()->value(), $friends, true)) {
                throw new AccessDeniedException();
            }
        }
    }
}
