<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Game\Create;

use AdnanMula\Cards\Application\Service\Deck\UpdateDeckWinRateService;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGame;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameLog;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\KeyforgeGameRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeCompetition;
use AdnanMula\Cards\Domain\Model\Keyforge\Game\ValueObject\KeyforgeGameScore;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUser;
use AdnanMula\Cards\Domain\Model\Keyforge\User\KeyforgeUserRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\CompositeFilter;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\NullFilterValue;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CreateGameCommandHandler
{
    private const array COMPETITIONS_VS_RANDOMS = [
        KeyforgeCompetition::VT,
        KeyforgeCompetition::TCO_CASUAL,
        KeyforgeCompetition::TCO_COMPETITIVE,
        KeyforgeCompetition::LGS,
        KeyforgeCompetition::NKFL,
    ];

    public function __construct(
        private KeyforgeUserRepository $userRepository,
        private KeyforgeGameRepository $gameRepository,
        private KeyforgeDeckRepository $deckRepository,
        private ImportDeckService $importDeckService,
        private UpdateDeckWinRateService $updateDeckWinRateService,
        private Security $security,
        private LoggerInterface $userActivityLogger,
    ) {}

    public function __invoke(CreateGameCommand $command): void
    {
        /** @var ?User $user */
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \Exception('Not authorized');
        }

        [$winner, $loser, $firstTurn] = $this->getUsers($user, $command->winner, $command->loser, $command->firstTurn, $command->competition);
        [$winnerDeck, $loserDeck] = $this->getDecks($command->winnerDeck, $command->loserDeck);

        $approved = false;

        if (\in_array($command->competition, self::COMPETITIONS_VS_RANDOMS, true)
            || ($command->winner === $command->loser && $command->competition === KeyforgeCompetition::SOLO)) {
            $approved = true;
        }

        $log = null;
        if (null !== $command->log) {
            try {
                $logParser = new GameLogParser();
                $game = $logParser->execute($command->log);
                $log = $game->rawLog;
            } catch (\Throwable) {
            }
        }

        $game = new KeyforgeGame(
            Uuid::v4(),
            $winner,
            $loser,
            $winnerDeck->id(),
            $loserDeck->id(),
            $command->winnerChains,
            $command->loserChains,
            $firstTurn,
            KeyforgeGameScore::from(3, $command->loserScore),
            $command->date,
            new \DateTimeImmutable(),
            $command->competition,
            $command->notes,
            $approved,
            $user->id(),
        );

        $this->gameRepository->save($game);

        if (null !== $log) {
            $this->gameRepository->saveLog(new KeyforgeGameLog(
                Uuid::v4(),
                $game->id(),
                $log,
                $user->id(),
                new \DateTimeImmutable(),
            ));
        }

        $this->updateDeckWinRateService->execute($winnerDeck->id());
        $this->updateDeckWinRateService->execute($loserDeck->id());

        $this->userActivityLogger->info('Game created', ['id' => $game->id()->value(), 'user' => $user->id()->value()]);
    }

    private function getUsers(User $user, string $winner, string $loser, string $firstTurn, KeyforgeCompetition $competition): array
    {
        $create = \in_array($competition, self::COMPETITIONS_VS_RANDOMS, true);
        $checkFirstLevel = \in_array($competition, self::COMPETITIONS_VS_RANDOMS, true);

        if (false === Uuid::isValid($winner)) {
            $winner = $this->fetchUserOrCreate($winner, $user, $create, $checkFirstLevel)->id()->value();
        }

        if (false === Uuid::isValid($loser)) {
            $loser = $this->fetchUserOrCreate($loser, $user, $create, $checkFirstLevel)->id()->value();
        }

        if (false === Uuid::isValid($firstTurn)) {
            $firstTurn = $this->fetchUserOrCreate($firstTurn, $user, $create, true)->id()->value();
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

    private function fetchUserOrCreate(string $name, User $user, bool $create, bool $checkFirstLevel = false): KeyforgeUser
    {
        $filterGroups = [
            new Filter(new FilterField('name'), new StringFilterValue($name), FilterOperator::EQUAL),
        ];

        if ($checkFirstLevel) {
            $filterGroups[] = new CompositeFilter(
                FilterType::OR,
                new Filter(new FilterField('owner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL),
                new Filter(new FilterField('owner'), new NullFilterValue(), FilterOperator::IS_NULL),
            );
        } else {
            $filterGroups[] = new Filter(new FilterField('owner'), new StringFilterValue($user->id()->value()), FilterOperator::EQUAL);
        }

        $userToCreate = $this->userRepository->searchOne(new Criteria(new Filters(FilterType::AND, ...$filterGroups)));

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
        $winnerDeck = $this->parseDeck($winnerDeck);

        $winnerDeck = Uuid::isValid($winnerDeck)
            ? $this->importDeckService->execute(Uuid::from($winnerDeck), null)
            : $this->getDeckByName($winnerDeck);

        $loserDeck = $this->parseDeck($loserDeck);

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
                new Filters(
                    FilterType::AND,
                    new Filter(new FilterField('name'), new StringFilterValue($name), FilterOperator::EQUAL),
                ),
            ),
        );
    }

    private function parseDeck(?string $idOrLink): ?string
    {
        if (null === $idOrLink) {
            return null;
        }

        if (Uuid::isValid($idOrLink)) {
            return $idOrLink;
        }

        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/alliance-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/alliance-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/theoretical-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/theoretical-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);

        return $idOrLink;
    }
}
