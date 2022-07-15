<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Import\ImportDeckCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ImportDeckController extends Controller
{
    public function __invoke(Request $request): Response
    {
        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => false, 'success' => null]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $this->bus->dispatch(new ImportDeckCommand($request->request->get('deck')));

                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Que bien jugado caralmendra', 'success' => true]);
            } catch (InvalidUuidStringException $exception) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Esto  no es un id, melón', 'success' => false]);
            } catch (\Throwable $exception) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => $exception->getMessage(), 'success' => false]);
            }
        }

        throw new \InvalidArgumentException('Error');
    }
}
