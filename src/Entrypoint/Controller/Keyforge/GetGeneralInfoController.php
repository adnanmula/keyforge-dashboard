<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge;

use AdnanMula\Cards\Application\Keyforge\GeneralData\GetGeneralKeyforgeDataCommand;
use AdnanMula\Cards\Entrypoint\Controller\Shared\QueryController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetGeneralInfoController extends QueryController
{
    public function __invoke(Request $request): Response
    {
        $data = $this->extractResult(
            $this->bus->dispatch(new GetGeneralKeyforgeDataCommand()),
        );

        return $this->render('Keyforge/general_data.html.twig', ['data' => $data],);
    }
}
