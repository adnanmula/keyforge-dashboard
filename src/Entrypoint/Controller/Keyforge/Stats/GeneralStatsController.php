<?php declare(strict_types=1);

namespace AdnanMula\Cards\Entrypoint\Controller\Keyforge\Stats;

use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\ValueObject\KeyforgeSet;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use AdnanMula\Cards\Entrypoint\Controller\Shared\Controller;
use Psr\Log\LoggerInterface;
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
        LoggerInterface $logger,
        private readonly KeyforgeDeckRepository $deckRepository,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($bus, $security, $localeSwitcher, $translator, $logger);
    }

    public function __invoke(): Response
    {
        $user = $this->getUser();

        [$houses, $sets, $wrBySet, $wrBySas, $wrByHouse, $avgStatsBySet] = $this->deckRepository->homeCounts();

        \ksort($houses, \SORT_STRING);
        \ksort($sets, \SORT_STRING);

        $indexedFriends = [];

        if (null !== $user) {
            $friends = $this->userRepository->friends($user->id(), false);

            foreach ($friends as $friend) {
                $indexedFriends[$friend['id']] = $friend['sender_name'];
                $indexedFriends[$friend['friend_id']] = $friend['receiver_name'];
            }
        }

        return $this->render(
            'Keyforge/Stats/general_stats.html.twig',
            [
                'houses' => $houses,
                'sets' => $sets,
                'wrBySet' => $this->winrateBySet($wrBySet),
                'wrByHouse' => $wrByHouse,
                'wrBySas' => $this->winrateBySas($wrBySas),
                'avgStatsBySet' => $this->avgStatsBySet($avgStatsBySet),
                'indexed_friends' => $indexedFriends,
            ],
        );
    }

    public function winrateBySas(mixed $wrBySas): array
    {
        $ranges = [30 => 39, 40 => 49, 50 => 59, 60 => 69, 70 => 79, 80 => 89, 90 => 99, 100 => 130];
        $wrBySasOrdered = [];

        foreach ($wrBySas as $key => $value) {
            foreach ($ranges as $from => $to) {
                if ($key >= $from && $key <= $to) {
                    $resultKey = $from . '-' . $to;

                    if (false === \array_key_exists($resultKey, $wrBySasOrdered)) {
                        $wrBySasOrdered[$resultKey] = ['wins' => 0, 'losses' => 0];
                    }

                    $wrBySasOrdered[$resultKey]['wins'] += $value['wins'];
                    $wrBySasOrdered[$resultKey]['losses'] += $value['losses'];
                }
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
            KeyforgeSet::ToC->value => 0,
            KeyforgeSet::AS->value => 0,
            KeyforgeSet::PV->value => 0,
        ];

        foreach ($wrBySet as $key => $value) {
            if (false === \array_key_exists($key, $wrBySetOrdered)) {
                continue;
            }

            $wrBySetOrdered[$key] = $value;
        }

        return $wrBySetOrdered;
    }

    private function avgStatsBySet(mixed $avgStatsBySet): array
    {
        $avgStatsBySetOrdered = [
            KeyforgeSet::CotA->value => 0,
            KeyforgeSet::AoA->value => 0,
            KeyforgeSet::WC->value => 0,
            KeyforgeSet::MM->value => 0,
            KeyforgeSet::DT->value => 0,
            KeyforgeSet::WoE->value => 0,
            KeyforgeSet::GR->value => 0,
            KeyforgeSet::ToC->value => 0,
            KeyforgeSet::AS->value => 0,
            KeyforgeSet::PV->value => 0,
        ];

        foreach ($avgStatsBySet as $key => $value) {
            if (false === \array_key_exists($key, $avgStatsBySetOrdered)) {
                continue;
            }

            $avgStatsBySetOrdered[$key] = $value;
        }

        return $avgStatsBySetOrdered;
    }
}
