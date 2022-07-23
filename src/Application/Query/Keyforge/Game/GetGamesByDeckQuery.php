<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;
use AdnanMula\Cards\Domain\Model\Shared\ValueObject\Uuid;
use Assert\Assert;

final class GetGamesByDeckQuery
{
    private Uuid $deckId;
    private ?Pagination $pagination;
    private ?SearchTerms $searchTerms;
    private ?QueryOrder $order;

    public function __construct($deckId, ?Pagination $pagination, ?SearchTerms $searchTerms, ?QueryOrder $order)
    {
        Assert::lazy()->that($deckId, 'deck_id')->uuid();

        $this->deckId = Uuid::from($deckId);
        $this->pagination = $pagination;
        $this->searchTerms = $searchTerms;
        $this->order = $order;
    }

    public function deckId(): Uuid
    {
        return $this->deckId;
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function searchTerms(): ?SearchTerms
    {
        return $this->searchTerms;
    }

    public function order(): ?QueryOrder
    {
        return $this->order;
    }
}
