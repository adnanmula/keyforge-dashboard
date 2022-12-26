<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Command;

use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTag;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile05;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile60;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile70;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile80;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile90;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeTagPercentile99;
use AdnanMula\Cards\Infrastructure\Criteria\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ApplyTagsCommand extends Command
{
    public const NAME = 'tags:apply';

    public function __construct(private readonly KeyforgeDeckRepository $deckRepository)
    {
        parent::__construct(self::NAME);
    }

    protected function configure(): void
    {
        $this->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $criteria = new Criteria(
            null,
            null,
            null,
        );

        $decks = $this->deckRepository->search($criteria);

        foreach ($decks as $deck) {
            $data = $deck->extraData()['deck'];
            $sasPercentile = $data['sasPercentile'];

            $newTags = [];

            if ($sasPercentile <= 5) {
                $newTags[] = new KeyforgeTagPercentile05();
            }

            if ($sasPercentile >= 60 && $sasPercentile < 70) {
                $newTags[] = new KeyforgeTagPercentile60();
            }

            if ($sasPercentile >= 70 && $sasPercentile < 80) {
                $newTags[] = new KeyforgeTagPercentile70();
            }

            if ($sasPercentile >= 80 && $sasPercentile < 90) {
                $newTags[] = new KeyforgeTagPercentile80();
            }

            if ($sasPercentile >= 90 && $sasPercentile < 99) {
                $newTags[] = new KeyforgeTagPercentile90();
            }

            if ($sasPercentile >= 99) {
                $newTags[] = new KeyforgeTagPercentile99();
            }

            $this->deckRepository->assignTags($deck->id(), $this->mergeTags($deck->tags(), $newTags));
        }

        return self::SUCCESS;
    }

    private function mergeTags(array $currentTags, array $newTags): array
    {
        return \array_values(\array_unique(
            \array_merge($currentTags, \array_map(static fn (KeyforgeTag $tag): string => $tag->id->value(), $newTags))
        ));
    }
}
