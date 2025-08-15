<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Competition\Join;

use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeCompetitionRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\CompetitionVisibility;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\Tournament\User as TournamentUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class JoinCompetitionCommandHandler
{
    public function __construct(
        private KeyforgeCompetitionRepository $repository,
        private UserRepository $userRepository,
        private TranslatorInterface $translator,
        private Security $security,
    ) {}

    public function __invoke(JoinCompetitionCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new AccessDeniedException();
        }

        $competition = $this->repository->searchOne(
            new Criteria(
                new Filters(
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
                [new TournamentUser($user->id()->value(), $user->name())],
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
                        $this->userRepository->friends(Uuid::from($admin->id)),
                    ),
                );
            }

            if (false === \in_array($user->id()->value(), $friends, true)) {
                throw new AccessDeniedException();
            }
        }
    }
}
