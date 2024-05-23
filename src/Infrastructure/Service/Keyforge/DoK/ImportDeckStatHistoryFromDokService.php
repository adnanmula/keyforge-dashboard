<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckStatHistoryRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\ValueObject\KeyforgeDeckStatHistory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
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
        $deck = $this->repository->byId($id);

        try {
            $response = $this->dokClient->request(
                Request::METHOD_GET,
                '/api/decks/past-sas/' . $deck->data()->dokId,
            )->toArray();
        } catch (\Throwable) {
            throw new \Exception('Error desconocido');
        }

        foreach ($response as $data) {
            $this->historyRepository->save(KeyforgeDeckStatHistory::fromDokData($deck->id(), $data));
        }
    }
}
