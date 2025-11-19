<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Game\Detail;

use AdnanMula\Cards\Application\Command\Keyforge\Game\Analyze\AnalyzeGameCommand;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use AdnanMula\KeyforgeGameLogParser\Parser\GameLogParser;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GameAnalyzeController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        private readonly LoggerInterface $userActivityLogger,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(Request $request): Response
    {
        $log = $request->get('log');

        if (null === $log) {
            return $this->render(
                'Keyforge/Game/Detail/game_analyze.html.twig',
                [
                    'error' => null,
                ],
            );
        }

        try {
            if (false === $this->isCsrfTokenValid('keyforge_game_analyze', $request->get('_csrf_token'))) {
                throw new \Exception('Invalid CSRF token');
            }

            $p = new GameLogParser();
            $parsedLog = $p->execute($log);

            if (null === $parsedLog->winner()) {
                throw new \Exception('Incomplete or malformed log');
            }

            $logId = Uuid::v4();

            $this->bus->dispatch(new AnalyzeGameCommand($logId->value(), null, $log));
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());

            return $this->render(
                'Keyforge/Game/Detail/game_analyze.html.twig',
                [
                    'error' => $this->translator->trans('menu.log_error'),
                ],
            );
        }

        $this->userActivityLogger->info('Game analyzed', ['user' => $this->getUser()?->id()->value()]);

        return $this->redirectToRoute('keyforge_game_log', ['id' => $logId->value()]);
    }
}
