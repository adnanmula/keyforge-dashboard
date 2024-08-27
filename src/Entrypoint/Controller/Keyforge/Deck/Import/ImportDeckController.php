<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\Import;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Import\ImportDeckCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ImportDeckController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $this->assertIsLogged();

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render('Keyforge/Deck/Import/import_deck.html.twig', ['result' => false, 'success' => null]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                $deckId = $this->parseDeck($request->request->get('deck'));
                $deckType = $request->request->get('deckType');
                $token = $request->request->get('token');
                $userId = null;

                if (null !== $request->get('setUser') || null !== $token) {
                    $user = $this->getUser();
                    $userId = $user->id()->value();
                }

                $this->bus->dispatch(new ImportDeckCommand($deckId, $deckType, $token, $userId));

                return $this->render('Keyforge/Deck/Import/import_deck.html.twig', ['result' => 'Bien', 'success' => true, 'deckId' => $deckId]);
            } catch (InvalidUuidStringException) {
                return $this->render('Keyforge/Deck/Import/import_deck.html.twig', ['result' => 'Id/link no válido :/', 'success' => false, 'deckId' => null]);
            } catch (\Throwable $exception) {
                return $this->render('Keyforge/Deck/Import/import_deck.html.twig', ['result' => $exception->getMessage(), 'success' => false, 'deckId' => null]);
            }
        }

        throw new \InvalidArgumentException('Error');
    }

    private function parseDeck(?string $idOrLink): ?string
    {
        if (null === $idOrLink) {
            return null;
        }

        if (Uuid::isValid($idOrLink)) {
            return $idOrLink;
        }

        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/alliance-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/alliance-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/decksofkeyforge.com\/theoretical-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/decksofkeyforge.com\/theoretical-decks\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/https:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);
        $idOrLink = \preg_replace('/http:\/\/www.keyforgegame.com\/deck-details\//i', '', $idOrLink);

        return $idOrLink;
    }
}
