<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Stat;

use AdnanMula\Cards\Application\Command\Keyforge\Stat\General\GenerateGeneralStatsCommand;
use AdnanMula\Cards\Application\Command\Keyforge\Stat\User\GenerateUserStatsCommand;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\KeyforgeStatRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Stat\ValueObject\KeyforgeStatCategory;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateStatProjectionsCommand extends Command
{
    public const NAME = 'projection:stat';

    public function __construct(
        private readonly KeyforgeStatRepository $repository,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Generate projections')
            ->addOption('category', 'c', InputOption::VALUE_REQUIRED)
            ->addOption('reference', 'r', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        [$category, $reference] = $this->params($input);

        if (null === $category && null === $reference) {
            $pending = $this->repository->queuedProjections();

            foreach ($pending as $projection) {
                if ($projection['category'] === KeyforgeStatCategory::HOME_GENERAL_DATA->value) {
                    $this->bus->dispatch(new GenerateGeneralStatsCommand());
                    $this->repository->removeQueuedProjection(KeyforgeStatCategory::HOME_GENERAL_DATA);
                }

                if ($projection['category'] === KeyforgeStatCategory::USER_PROFILE->value) {
                    $this->bus->dispatch(new GenerateUserStatsCommand(Uuid::v4()->value()));
                    $this->repository->removeQueuedProjection(KeyforgeStatCategory::USER_PROFILE);
                }
            }
        }

        return self::SUCCESS;
    }

    private function params(InputInterface $input): array
    {
        $category = $input->getOption('category');
        $reference = $input->getOption('reference');

        return [$category, $reference];
    }
}
