<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command\Deck;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeck;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\Deck\DeckApplyPredefinedTagsService;
use AdnanMula\Criteria\Criteria;
use AdnanMula\Criteria\Filter\Filter;
use AdnanMula\Criteria\Filter\FilterType;
use AdnanMula\Criteria\FilterField\FilterField;
use AdnanMula\Criteria\FilterGroup\AndFilterGroup;
use AdnanMula\Criteria\FilterValue\FilterOperator;
use AdnanMula\Criteria\FilterValue\StringFilterValue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class SetDeckPredefinedTagsCommand extends Command
{
    public const NAME = 'deck:tag:set';

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
            $output->writeln($deck->data()->name);
        }

        return self::SUCCESS;
    }

    /** @return array<KeyforgeDeck> */
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
            new Criteria(null, null, null, new AndFilterGroup(FilterType::AND, ...$filters)),
        );
    }
}
