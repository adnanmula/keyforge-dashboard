<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Deck;

use AdnanMula\Cards\Application\Service\Json;
use AdnanMula\Cards\Domain\Model\Keyforge\Deck\KeyforgeDeckRepository;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\UserRole;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class GetDecksTagsQueryHandler
{
    public function __construct(
        private KeyforgeDeckRepository $deckRepository,
        private Security $security,
    ) {}

    public function __invoke(GetDecksTagsQuery $query): array
    {
        $this->security->isGranted(UserRole::ROLE_KEYFORGE);

        $ownerData = $this->deckRepository->ownedBy($query->userId);
        $result = [];

        foreach ($ownerData as $ownerDatum) {
            $ownerDatum['user_tags'] = Json::decode($ownerDatum['user_tags']);
            $result[$ownerDatum['deck_id']] = $ownerDatum;
        }

        return $result;
    }
}
