<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\List;

use AdnanMula\Cards\Application\Query\Keyforge\Game\GetGameStatsQuery;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Assert\LazyAssertionException;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetGameStatsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $allParams = new InputBag(\array_merge($request->query->all(), $request->request->all()));

        $userId = $allParams->get('userId');
        if ('' === $userId) {
            $userId = null;
        }

        $deckId = $allParams->get('deckId');
        if ('' === $deckId) {
            $deckId = null;
        }

        $logStatKeys = [
            'turnsMin', 'turnsMax',
            'winnerAmberObtainedMin', 'winnerAmberObtainedMax',
            'winnerAmberStolenMin', 'winnerAmberStolenMax',
            'winnerCardsPlayedMin', 'winnerCardsPlayedMax',
            'winnerCardsDrawnMin', 'winnerCardsDrawnMax',
            'winnerCardsDiscardedMin', 'winnerCardsDiscardedMax',
            'winnerKeysForgedMin', 'winnerKeysForgedMax',
            'winnerFightsMin', 'winnerFightsMax',
            'winnerReapsMin', 'winnerReapsMax',
            'winnerExtraTurnsMin', 'winnerExtraTurnsMax',
            'loserAmberObtainedMin', 'loserAmberObtainedMax',
            'loserAmberStolenMin', 'loserAmberStolenMax',
            'loserCardsPlayedMin', 'loserCardsPlayedMax',
            'loserCardsDrawnMin', 'loserCardsDrawnMax',
            'loserCardsDiscardedMin', 'loserCardsDiscardedMax',
            'loserKeysForgedMin', 'loserKeysForgedMax',
            'loserFightsMin', 'loserFightsMax',
            'loserReapsMin', 'loserReapsMax',
            'loserExtraTurnsMin', 'loserExtraTurnsMax',
            'totalAmberObtainedMin', 'totalAmberObtainedMax',
            'totalAmberStolenMin', 'totalAmberStolenMax',
            'totalCardsPlayedMin', 'totalCardsPlayedMax',
            'totalCardsDrawnMin', 'totalCardsDrawnMax',
            'totalCardsDiscardedMin', 'totalCardsDiscardedMax',
            'totalKeysForgedMin', 'totalKeysForgedMax',
            'totalFightsMin', 'totalFightsMax',
            'totalReapsMin', 'totalReapsMax',
            'totalExtraTurnsMin', 'totalExtraTurnsMax',
        ];

        $logStats = [];
        foreach ($logStatKeys as $key) {
            $value = $allParams->get($key);
            if ($value !== null && $value !== '') {
                $logStats[$key] = $value;
            }
        }

        try {
            $stats = $this->extractResult($this->bus->dispatch(new GetGameStatsQuery(
                userId: $userId,
                deckId: $deckId,
                winners: $allParams->all()['extraFilterWinner'] ?? [],
                losers: $allParams->all()['extraFilterLoser'] ?? [],
                loserScores: $allParams->all()['extraFilterScore'] ?? [],
                competitions: $allParams->all()['extraFilterCompetition'] ?? [],
                dateFrom: $allParams->get('extraFilterDateFrom') ?: null,
                dateTo: $allParams->get('extraFilterDateTo') ?: null,
                logStats: $logStats,
            )));
        } catch (LazyAssertionException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }

        return new JsonResponse($stats);
    }
}
