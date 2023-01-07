<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Deck\Analyze;

use AdnanMula\Cards\Application\Command\Keyforge\Deck\Analyze\AnalyzeDeckThreatsCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class AnalyzeDeckController extends Controller
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
            return $this->render('Keyforge/Deck/Analyze/analyze_deck.html.twig', [
                'loaded' => false,
                'deck_id' => null,
                'deck_name' => null,
                'deck_sas' => null,
                'detail' => null,
            ]);
        }

        $data = $this->extractResult(
            $this->bus->dispatch(new AnalyzeDeckThreatsCommand(
                $this->parseDeck($request->request->get('deck')),
            )),
        );

        return $this->render('Keyforge/Deck/Analyze/analyze_deck.html.twig', [
            'loaded' => true,
            'deck_id' => $data['deck_id'],
            'deck_name' => $data['deck_name'],
            'deck_sas' => $data['deck_sas'],
            'detail' => $data['detail'],
        ]);
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
        $idOrLink = \preg_replace('/http:\/\/www.adnanmula.com\/keyforge\/games\/deck\//i', '', $idOrLink);

        return $idOrLink;
    }
}
