<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Import\ImportDeckCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
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
                /** @var User $user */
                $user = $this->getUser();
                $deckId = $this->parseDeck($request->request->get('deck'));

                $this->bus->dispatch(new ImportDeckCommand($deckId, $user->id()->value()));

                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Que bien jugado caralmendra, ir a barajas', 'success' => true]);
            } catch (InvalidUuidStringException) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Esto  no es un id, melón', 'success' => false]);
            } catch (\Throwable $exception) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => $exception->getMessage(), 'success' => false]);
            }
        }

        throw new \InvalidArgumentException('Error');
    }

    private function parseDeck(string $idOrLink): string
    {
        if (Uuid::isValid($idOrLink)) {
            return $idOrLink;
        }

        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);

        return $idOrLink;
    }
}
