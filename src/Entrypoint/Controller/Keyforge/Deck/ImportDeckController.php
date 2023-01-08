<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Import\ImportDeckCommand;
use AdnanMula\Cards\Domain\Model\Shared\User;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ImportDeckController extends Controller
{
    private Security $security;

    public function __construct(MessageBusInterface $bus, Security $security)
    {
        $this->security = $security;

        parent::__construct($bus);
    }

    public function __invoke(Request $request): Response
    {
        if (false === $this->security->isGranted('ROLE_KEYFORGE')) {
            throw new AccessDeniedException();
        }

        if ($request->getMethod() === Request::METHOD_GET) {
            return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => false, 'success' => null]);
        }

        if ($request->getMethod() === Request::METHOD_POST) {
            try {
                /** @var User $user */
                $user = $this->getUser();
                $deckId = $this->parseDeck($request->request->get('deck'));

                $this->bus->dispatch(new ImportDeckCommand($deckId, $user->id()->value()));

                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Bien', 'success' => true, 'deckId' => $deckId]);
            } catch (InvalidUuidStringException) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => 'Id/link no válido :/', 'success' => false, 'deckId' => null]);
            } catch (\Throwable $exception) {
                return $this->render('Keyforge/Deck/import_deck.html.twig', ['result' => $exception->getMessage(), 'success' => false, 'deckId' => null]);
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
