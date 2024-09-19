<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\Alliance;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\GenerateAlliances\GenerateDeckAlliancesCommand;
use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GenerateAlliancesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render('Keyforge/Deck/Alliance/generate_alliances.html.twig', ['result' => false, 'success' => null]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $payload = Json::decode($request->getContent());

                $this->bus->dispatch(new GenerateDeckAlliancesCommand($payload['decks'], $payload['houses']));

                return new JsonResponse(['success' => true]);
            } catch (InvalidUuidStringException) {
                return new JsonResponse(['error' => 'Invalid uuid']);
            } catch (\Throwable $e) {
                return new JsonResponse(['error' => $e->getMessage()]);
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
