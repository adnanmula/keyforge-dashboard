<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Query\Keyforge\Competition\GetCompetitionDetailQuery;
use AdnanMula\Cards\Application\Query\Keyforge\User\GetUsersQuery;
use AdnanMula\Cards\Domain\Model\Keyforge\KeyforgeUser;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompetitionDetailController extends Controller
{
    public function __invoke(Request $request, string $reference): Response
    {
        $users = $this->extractResult(
            $this->bus->dispatch(new GetUsersQuery(null, null, false, false)),
        );

        $detail = $this->extractResult(
            $this->bus->dispatch(new GetCompetitionDetailQuery($reference)),
        );

        return $this->render('Keyforge/Competition/competition_detail.html.twig', [
            'users' => \array_map(static fn (KeyforgeUser $user) => $user->jsonSerialize(), $users),
            'competition' => $detail['competition'],
            'fixtures' => $detail['fixtures'],
            'classification' => $detail['classification'],
        ]);
    }
}