<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Card;

use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCard;
use AdnanMula\Cards\Domain\Model\Keyforge\Card\KeyforgeCardRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Locale;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ImportCardsCommand extends Command
{
    public const string NAME = 'import:card';

    public function __construct(
        private HttpClientInterface $dokClient,
        private KeyforgeCardRepository $repository,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Import cards');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->dokClient->request(Request::METHOD_GET, '/public-api/v1/cards')->toArray();

        foreach ($response as $cardData) {
            $card = KeyforgeCard::fromDokData($cardData);
            $this->repository->save($card);

            $output->writeln($card->name->get(Locale::en_GB));
        }

        return self::SUCCESS;
    }
}
