<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckStatHistoryRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckStatHistory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterOperator;
use AdnanMula\Criteria\Filter\Filters;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportDeckStatHistoryFromDokService
{
    public function __construct(
        private HttpClientInterface $dokClient,
        private KeyforgeDeckRepository $repository,
        private KeyforgeDeckStatHistoryRepository $historyRepository,
    ) {}

    public function execute(Uuid $id): void
    {
        $deck = $this->repository->searchOne(new Criteria(
            new Filters(
                FilterType::AND,
                new Filter(new FilterField('id'), new StringFilterValue($id->value()), FilterOperator::EQUAL),
            ),
        ));

        if (null === $deck) {
            return;
        }

        try {
            $response = $this->dokClient->request(
                Request::METHOD_GET,
                '/api/decks/past-sas/' . $deck->dokId(),
            )->toArray();
        } catch (\Throwable) {
            throw new \Exception('Error desconocido');
        }

        foreach ($response as $data) {
            $this->historyRepository->save(KeyforgeDeckStatHistory::fromDokData($deck->id(), $data));
        }
    }
}
