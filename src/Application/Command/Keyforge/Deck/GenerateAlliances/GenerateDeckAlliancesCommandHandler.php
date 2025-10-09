<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\GenerateAlliances;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckAllianceRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Link;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckAllianceService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringArrayFilterValue;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class GenerateDeckAlliancesCommandHandler
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private KeyforgeDeckAllianceRepository $deckAllianceRepository,
        private ImportDeckAllianceService $importDeckAllianceService,
        private HttpClientInterface $dokClient,
        private string $dokUser,
        private string $dokPass,
        private Security $security,
    ) {}

    public function __invoke(GenerateDeckAlliancesCommand $command): array
    {
        $this->security->isGranted(UserRole::ROLE_ADMIN);

        /** @var User $user */
        $user = $this->security->getUser();

        $decks = $this->deckRepository->search(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$command->deckIds()), FilterOperator::IN),
            ),
        ));

        $this->validations($command, ...$decks);

        $pods = $this->pods($command, ...$decks);
        $combinations = $this->combinations($pods, $command->addToOwnedDok);

        $importedDecks = [];

        $authToken = $this->login();

        foreach ($combinations as $combination) {
            $isAlreadyImported = $this->deckAllianceRepository->isAlreadyImported(
                $combination['houseOneDeckId'],
                $combination['houseOne'],
                $combination['houseTwoDeckId'],
                $combination['houseTwo'],
                $combination['houseThreeDeckId'],
                $combination['houseThree'],
            );

            if ($isAlreadyImported) {
                continue;
            }

            try {
                $response = $this->dokClient->request(Request::METHOD_POST, '/api/alliance-decks/secured', [
                    'headers' => [
                        'Authorization' => $authToken,
                    ],
                    'json' => $combination,
                ]);

                $importedDeckId = Uuid::from(\str_replace('"', '', $response->getContent()));
                $importedDeck = $this->importDeckAllianceService->execute($importedDeckId, $command->addToMyDecks ? $user->id() : null);
                $this->deckRepository->commit();
                $this->deckRepository->beginTransaction();

                $importedDecks[] = [
                    'id' => $importedDeckId->value(),
                    'url' => Link::dokDeckFromId(KeyforgeDeckType::ALLIANCE, $importedDeckId),
                    'deck' => $importedDeck,
                ];
            } catch (\Throwable) {
                throw new \Exception('Error desconocido');
            }
        }

        return [
            'combinations' => \count($combinations),
            'decks' => $importedDecks,
        ];
    }

    private function validations(GenerateDeckAlliancesCommand $command, ?KeyforgeDeck ...$decks): void
    {
        if (\count($decks) !== \count($command->deckIds())) {
            throw new \Exception('Missing deck');
        }

        if (0 === \count($decks)) {
            throw new \Exception('Deck error');
        }

        foreach ($decks as $deck) {
            if ($deck->set() !== $decks[0]->set()) {
                throw new \Exception('Set error');
            }
        }
    }

    private function pods(GenerateDeckAlliancesCommand $command, KeyforgeDeck ...$decks): array
    {
        $pods = [];

        foreach ($decks as $deck) {
            foreach ($deck->houses()->value() as $house) {
                if (false === \in_array($house->value, $command->housesOf($deck->id()->value()), true)) {
                    continue;
                }

                $pods[] = [
                    'id' => $deck->id()->value(),
                    'house' => $house->dokName(),
                ];
            }
        }

        return $pods;
    }

    private function combinations(array $data, bool $addToOwned): array
    {
        $count = \count($data);

        if ($count < 3) {
            return [];
        }

        $combinations = [];

        for ($i = 0; $i < $count - 2; ++$i) {
            for ($j = $i + 1; $j < $count - 1; ++$j) {
                for ($k = $j + 1; $k < $count; ++$k) {
                    $house1 = $data[$i]['house'];
                    $house2 = $data[$j]['house'];
                    $house3 = $data[$k]['house'];
                    $id1 = $data[$i]['id'];
                    $id2 = $data[$j]['id'];
                    $id3 = $data[$k]['id'];

                    $isNotDuplicate = $house1 !== $house2 && $house1 !== $house3 && $house2 !== $house3;
                    $isNotSameDeck = $id1 !== $id2 || $id1 !== $id3 || $id2 !== $id3;

                    if ($isNotDuplicate && $isNotSameDeck) {
                        $combinations[] = [
                            'houseOne' => $data[$i]['house'],
                            'houseOneDeckId' => $data[$i]['id'],
                            'houseTwo' => $data[$j]['house'],
                            'houseTwoDeckId' => $data[$j]['id'],
                            'houseThree' => $data[$k]['house'],
                            'houseThreeDeckId' => $data[$k]['id'],
                            'owned' => $addToOwned,
                        ];
                    }
                }
            }
        }

        return $combinations;
    }

    private function login(): string
    {
        $response = $this->dokClient->request(Request::METHOD_POST, '/api/users/login', [
            'json' => [
                'email' => $this->dokUser,
                'password' => $this->dokPass,
            ],
            'headers' => [
                'Accept'=>'application/json, text/plain, */*',
                'Content-Type'=>'application/json',
                'Referer'=>'https://decksofkeyforge.com/decks',
                'User-Agent'=>'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36',
            ],
        ]);

        $authToken = $response->getHeaders()['authorization'][0] ?? null;

        if (null === $authToken) {
            throw new \Exception('dep');
        }

        return $authToken;
    }
}
