<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Competition;

use AdnanMula\Cards\Application\Query\Keyforge\Competition\GetCompetitionsQuery;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetCompetitionsController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $this->getUserWithRole(UserRole::ROLE_KEYFORGE);

        $result = $this->extractResult(
            $this->bus->dispatch(new GetCompetitionsQuery(
                $request->get('start', 0),
                $request->get('length', 10),
            )),
        );

        $response = [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['totalFiltered'],
            'data' => $result['competitions'],
            'draw' => (int) $request->get('draw'),
        ];

        return new JsonResponse($response);
    }
}
