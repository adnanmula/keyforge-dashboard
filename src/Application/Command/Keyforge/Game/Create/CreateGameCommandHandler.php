<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateGameCommandHandler
{
    private const COMPETITIONS_VS_RANDOMS = [
        KeyforgeCompetition::VT,
        KeyforgeCompetition::TCO_CASUAL,
        KeyforgeCompetition::TCO_COMPETITIVE,
    ];

    public function __construct(
        private KeyforgeUserRepository $userRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private ImportDeckService $importDeckService,
        private UpdateDeckWinRateService $updateDeckWinRateService,
        private Security $security,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Not authorized');
        }

        [$winner, $loser, $firstTurn] = $this->getUsers($user, $command->winner(), $command->loser(), $command->firstTurn(), $command->competition());
        [$winnerDeck, $loserDeck] = $this->getDecks($command->winnerDeck(), $command->loserDeck());

        $approved = false;

        if (\in_array($command->competition(), self::COMPETITIONS_VS_RANDOMS, true)
            || ($command->winner() === $command->loser() && $command->competition() === KeyforgeCompetition::SOLO)) {
            $approved = true;
        }

        $game = new KeyforgeGame(
            Uuid::v4(),
            $winner,
            $loser,
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->winnerChains(),
            $command->loserChains(),
            $firstTurn,
            KeyforgeGameScore::from(3, $command->loserScore()),
            $command->date(),
            new \DateTimeImmutable(),
            $command->competition(),
            $command->notes(),
            $approved,
            $user->id(),
        );

        $this->gameRepository->save($game);

        $this->updateDeckWinRateService->execute($winnerDeck->id());
        $this->updateDeckWinRateService->execute($loserDeck->id());
    }

    private function getUsers(User $user, string $winner, string $loser, string $firstTurn, KeyforgeCompetition $competition): array
    {
        $create = \in_array($competition, self::COMPETITIONS_VS_RANDOMS, true);

        if (false === Uuid::isValid($winner)) {
            $winner = $this->fetchUserOrCreate($winner, $user, $create)->id()->value();
        }

        if (false === Uuid::isValid($loser)) {
            $loser = $this->fetchUserOrCreate($loser, $user, $create)->id()->value();
        }

        if (false === Uuid::isValid($firstTurn)) {
            $firstTurn = $this->fetchUserOrCreate($firstTurn, $user, $create)->id()->value();
        }

        if (false === Uuid::isValid($winner)
            || false === Uuid::isValid($loser)
            || false === Uuid::isValid($firstTurn)) {
            throw new \Exception('Invalid user');
        }

        if ($firstTurn !== $winner && $firstTurn !== $loser) {
            throw new \InvalidArgumentException('First player is not in game');
        }

        return [Uuid::from($winner), Uuid::from($loser), Uuid::from($firstTurn)];
    }

    private function fetchUserOrCreate(string $name, User $user, bool $create): KeyforgeUser
    {
        $userToCreate = $this->userRepository->search(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('name'), new StringFilterValue($name), FilterOperator::EQUAL),
                    new Filter(new FilterField('owner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                ),
            ),
        )[0] ?? null;

        if (null === $userToCreate && true === $create) {
            $userToCreate = KeyforgeUser::create(Uuid::v4(), $name, $user->id());
            $this->userRepository->save($userToCreate);
        }

        if (null === $userToCreate) {
            throw new \Exception('User not found');
        }

        return $userToCreate;
    }

    private function getDecks(string $winnerDeck, string $loserDeck): array
    {
        $winnerDeck = Uuid::isValid($winnerDeck)
            ? $this->importDeckService->execute(Uuid::from($winnerDeck), null)
            : $this->getDeckByName($winnerDeck);

        $loserDeck = Uuid::isValid($loserDeck)
            ? $this->importDeckService->execute(Uuid::from($loserDeck), null)
            : $this->getDeckByName($loserDeck);

        if (null === $winnerDeck || null === $loserDeck) {
            throw new \Exception('Deck not found');
        }

        return [$winnerDeck, $loserDeck];
    }

    private function getDeckByName(string $name): ?KeyforgeDeck
    {
        return $this->deckRepository->searchOne(
            new Criteria(
                null,
                null,
                null,
                new AndFilterGroup(
                    FilterType::AND,
                    new Filter(new FilterField('name'), new StringFilterValue($name), FilterOperator::EQUAL),
                ),
            ),
        );
    }
}
