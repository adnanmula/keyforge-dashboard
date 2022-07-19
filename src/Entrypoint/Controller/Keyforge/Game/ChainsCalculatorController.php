<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Domain\Service\Keyforge\ChainsCalculator;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

final class ChainsCalculatorController extends Controller
{
    private ChainsCalculator $calculator;

    public function __construct(MessageBusInterface $bus, ChainsCalculator $calculator)
    {
        $this->calculator = $calculator;

        parent::__construct($bus);
    }

    public function __invoke(Request $request): Response
    {
        $user1 = Uuid::from('426117e9-e016-4f53-be1f-4eb8711ce625');
        $user2 = Uuid::from('97a7e9fe-ff27-4d52-83c0-df4bc9309fb0');
        $deck1 = Uuid::from('aa99749f-79b3-4040-8cd7-5c824cf3da3b');
        $deck2 = Uuid::from('deb90365-d69e-4ed4-9bf9-796320230ebb');

        $this->calculator->execute($user1, $user2, $deck1, $deck2);

        return new Response('');
    }
}
