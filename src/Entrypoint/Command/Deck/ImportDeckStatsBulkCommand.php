<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception\DeckNotExistsException;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeDeckType;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;
use AdnanMula\Cards\Infrastructure\Persistence\Repository\Keyforge\Deck\KeyforgeDeckUpdateDbalRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ImportDeckStatsBulkCommand extends Command
{
    public const string NAME = 'import:deck';

    public function __construct(
        private readonly Connection $connection,
        private readonly KeyforgeDeckUpdateDbalRepository $updateRepository,
        private readonly ImportDeckService $service,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Import decks')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Amount of decks to process', 10)
            ->addOption('with-history', null, InputOption::VALUE_NONE, 'Import stats history')
            ->addOption('decks', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$batch, $withHistory, $deckIds] = $this->params($input);

        $alreadyImported = $this->updateRepository->all();
        $decks = $this->decks($batch, $deckIds, $alreadyImported);

        foreach ($decks as $index => $deck) {
            try {
                $this->service->execute(Uuid::from($deck), null, true, $withHistory);
                $output->writeln($deck);
            } catch (DeckNotExistsException) {
                $output->writeln('<error>NOT FOUND: '. $deck .'</error>');
            }

            $this->updateRepository->add(Uuid::from($deck));

            if ($index*2 > 0 && ($index*2+2) % 25 === 0) {
                $output->writeln('Reached request limit sleeping for 70 seconds');
                \sleep(70);
            }
        }

        return self::SUCCESS;
    }

    private function params(InputInterface $input): array
    {
        $batch = (int) $input->getArgument('batch');
        $withHistory = $input->getOption('with-history') ?? false;
        $deckIds = $input->getOption('decks') ?? [];

        if ([] !== $deckIds) {
            $deckIds = \explode(',', $input->getOption('decks'));
        }

        return [$batch, $withHistory, $deckIds];
    }

    private function decks(int $batch, array $deckIds, array $alreadyImported): array
    {
        $draftDecks = [
            '37259b93-1cdd-4ea8-8206-767b071b2643',
            'dcbc4eae-b03b-4a75-a8ba-65742f1ca1c6',
            '19ee9a3b-cbe5-4fe5-b4a5-388a1cc3c37a',
            'eaa1eb19-6ec9-400f-8881-b88eeddd06bc',
        ];

        $alreadyImported = \array_merge($alreadyImported, $draftDecks);

        $query = $this->connection->createQueryBuilder()
            ->select('a.id')
            ->from('keyforge_decks', 'a')
            ->where('a.id not in (:already_imported)')
            ->andWhere('a.deck_type = :type')
            ->setParameter('already_imported', $alreadyImported, ArrayParameterType::STRING)
            ->setParameter('type', KeyforgeDeckType::STANDARD->value);

        if (\count($deckIds) > 0) {
            $query->andWhere('a.id in (:decks)')
                ->setParameter('decks', $deckIds, ArrayParameterType::STRING);
        }

        return $query->setMaxResults($batch)->executeQuery()->fetchFirstColumn();
    }
}
