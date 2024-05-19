<?php declare(strict_types=1);

namespace AdnanMula\Cards\Infrastructure\Service\Keyforge\DoK;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportPastDeckSasFromDokService
{
    public function __construct(
        private HttpClientInterface $dokClient,
    ) {}

    public function execute(Uuid $uuid, ?Uuid $owner = null, bool $forceUpdate = false): void
    {


    }
}
