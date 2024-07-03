<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GeneralStatsController extends Controller
{
    public function __construct(
        MessageBusInterface $bus,
        Security $security,
        LocaleSwitcher $localeSwitcher,
        TranslatorInterface $translator,
        private readonly KeyforgeDeckRepository $deckRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator);
    }

    public function __invoke(): Response
    {
        [$houses, $sets, $wrBySet, $wrBySas, $wrByHouse] = $this->deckRepository->homeCounts();

        \ksort($houses, \SORT_STRING);
        \ksort($sets, \SORT_STRING);

        return $this->render(
            'Keyforge/Stats/general_stats.html.twig',
            [
                'houses' => $houses,
                'sets' => $sets,
                'wrBySet' => $this->winrateBySet($wrBySet),
                'wrByHouse' => $wrByHouse,
                'wrBySas' => $this->winrateBySas($wrBySas),
            ],
        );
    }

    public function winrateBySas(mixed $wrBySas): array
    {
        $wrBySasOrdered = [
            '40-49' => ['wins' => 0, 'losses' => 0],
            '50-59' => ['wins' => 0, 'losses' => 0],
            '60-69' => ['wins' => 0, 'losses' => 0],
            '70-79' => ['wins' => 0, 'losses' => 0],
            '80-89' => ['wins' => 0, 'losses' => 0],
            '90-99' => ['wins' => 0, 'losses' => 0],
            '100-130' => ['wins' => 0, 'losses' => 0],
        ];

        foreach ($wrBySas as $key => $value) {
            if ($key >= 40 && $key <= 49) {
                $wrBySasOrdered['40-49']['wins'] += $value['wins'];
                $wrBySasOrdered['40-49']['losses'] += $value['losses'];
            }
            if ($key >= 50 && $key <= 59) {
                $wrBySasOrdered['50-59']['wins'] += $value['wins'];
                $wrBySasOrdered['50-59']['losses'] += $value['losses'];
            }
            if ($key >= 60 && $key <= 69) {
                $wrBySasOrdered['60-69']['wins'] += $value['wins'];
                $wrBySasOrdered['60-69']['losses'] += $value['losses'];
            }
            if ($key >= 70 && $key <= 79) {
                $wrBySasOrdered['70-79']['wins'] += $value['wins'];
                $wrBySasOrdered['70-79']['losses'] += $value['losses'];
            }
            if ($key >= 80 && $key <= 89) {
                $wrBySasOrdered['80-89']['wins'] += $value['wins'];
                $wrBySasOrdered['80-89']['losses'] += $value['losses'];
            }
            if ($key >= 90 && $key <= 99) {
                $wrBySasOrdered['90-99']['wins'] += $value['wins'];
                $wrBySasOrdered['90-99']['losses'] += $value['losses'];
            }
            if ($key >= 100 && $key <= 130) {
                $wrBySasOrdered['100-130']['wins'] += $value['wins'];
                $wrBySasOrdered['100-130']['losses'] += $value['losses'];
            }
        }

        foreach ($wrBySasOrdered as &$row) {
            if ($row['wins'] + $row['losses'] === 0) {
                $row['winrate'] = 0;
            } else {
                $row['winrate'] = $row['wins'] * 100 / ($row['wins'] + $row['losses']);
            }
        }

        return $wrBySasOrdered;
    }

    public function winrateBySet(mixed $wrBySet): array
    {
        $wrBySetOrdered = [
            KeyforgeSet::CotA->value => 0,
            KeyforgeSet::AoA->value => 0,
            KeyforgeSet::WC->value => 0,
            KeyforgeSet::MM->value => 0,
            KeyforgeSet::DT->value => 0,
            KeyforgeSet::WoE->value => 0,
            KeyforgeSet::GR->value => 0,
        ];

        foreach ($wrBySet as $key => $value) {
            if (false === \array_key_exists($key, $wrBySetOrdered)) {
                continue;
            }

            $wrBySetOrdered[$key] = $value;
        }

        return $wrBySetOrdered;
    }
}
