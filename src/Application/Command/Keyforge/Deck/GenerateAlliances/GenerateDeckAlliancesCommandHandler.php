<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\GenerateAlliances;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckAllianceRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckAllianceService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
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
            null,
            null,
            null,
            new AndFilterGroup(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringArrayFilterValue(...$command->deckIds), FilterOperator::IN),
            ),
        ));

        if (\count($decks) !== \count(array_unique($command->deckIds))) {
            throw new \Exception('Missing deck');
        }

        $this->validations(...$decks);

        $pods = $this->pods($command->deckHouses, ...$decks);
        $combinations = $this->combinations($pods);

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

                $this->importDeckAllianceService->execute(Uuid::from(\str_replace('"', '', $response->getContent())), $user->id());
                $this->deckRepository->commit();
                $this->deckRepository->beginTransaction();
            } catch (\Throwable) {
                throw new \Exception('Error desconocido');
            }
        }

        return [
            'combinations' => \count($combinations),
        ];
    }

    private function validations(?KeyforgeDeck ...$decks): void
    {
        if (0 === \count($decks)) {
            throw new \Exception('Deck error');
        }

        foreach ($decks as $deck) {
            if (null === $deck) {
                throw new \Exception('Deck error');
            }

            if ($deck->set() !== $decks[0]->set()) {
                throw new \Exception('Set error');
            }
        }
    }

    private function pods(array $houses, KeyforgeDeck ...$decks): array
    {
        $pods = [];

        foreach ($decks as $deck) {
            foreach ($deck->houses()->value() as $house) {
                if (false === \in_array($house->value, $houses[$deck->id()->value()] ?? [], true)) {
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

    private function combinations(array $data): array
    {
        $count = \count($data);

        if ($count < 3) {
            return [];
        }

        $combinations = [];

        for ($i = 0; $i < $count - 2; $i++) {
            for ($j = $i + 1; $j < $count - 1; $j++) {
                for ($k = $j + 1; $k < $count; $k++) {
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
                            'owned' => true,
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
