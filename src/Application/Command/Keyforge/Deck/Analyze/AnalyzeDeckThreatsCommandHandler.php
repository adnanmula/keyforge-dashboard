<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Command\Keyforge\Deck\Analyze;

use AdnanMula\Cards\Domain\Service\Keyforge\Analyzer\DeckAnalyzeService;
use AdnanMula\Cards\Domain\Service\Keyforge\ImportDeckService;

final readonly class AnalyzeDeckThreatsCommandHandler
{
    public function __construct(
        private ImportDeckService $importDeckService,
        private DeckAnalyzeService $analyzeService,
    ) {}

    public function __invoke(AnalyzeDeckThreatsCommand $command): array
    {
        $deck = $this->importDeckService->execute($command->deckId, null);

        if (null === $deck) {
            throw new \Exception('Deck not found');
        }

        $results = $this->analyzeService->execute($deck);

        return [
            'deck' => $deck->jsonSerialize(),
            'deck_id' => $deck->id()->value(),
            'deck_name' => $deck->name(),
            'deck_sas' => $deck->sas(),
            'detail' => $results,
        ];
    }
}
