<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Tag;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filter;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\Filters;
use AdnanMula\Cards\Infrastructure\Criteria\Filter\FilterType;
use AdnanMula\Cards\Infrastructure\Criteria\FilterField\FilterField;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\FilterOperator;
use AdnanMula\Cards\Infrastructure\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TagsApplyPredefinedCommand extends Command
{
    public const NAME = 'tags:predefined:apply';

    public function __construct(
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly DeckApplyPredefinedTagsService $service,
    ) {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('Apply predefined tags to decks')
            ->addOption('deck', 'd', InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $decks = $this->decks($input->getOption('deck'));

        foreach ($decks as $deck) {
            $this->service->execute($deck);
            $output->writeln($deck->name());
        }

        return self::SUCCESS;
    }

    private function decks(?string $deckId): array
    {
        $filters = [];

        if (null !== $deckId) {
            if (false === Uuid::isValid($deckId)) {
                throw new \InvalidArgumentException('Invalid deck id');
            }

            $filters[] = new Filter(new FilterField('id'), new StringFilterValue($deckId), FilterOperator::EQUAL);
        }

        return $this->deckRepository->search(
            new Criteria(null, null, null, new Filters(FilterType::AND, FilterType::AND, ...$filters)),
        );
    }
}
