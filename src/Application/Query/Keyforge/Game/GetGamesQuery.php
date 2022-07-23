<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\Query\Keyforge\Game;

use AdnanMula\Cards\Domain\Model\Shared\Pagination;
use AdnanMula\Cards\Domain\Model\Shared\QueryOrder;
use AdnanMula\Cards\Domain\Model\Shared\SearchTerms;

final class GetGamesQuery
{
    private ?Pagination $pagination;
    private ?SearchTerms $search;
    private ?QueryOrder $order;

    public function __construct(?Pagination $pagination, ?SearchTerms $search, ?QueryOrder $order)
    {
        $this->pagination = $pagination;
        $this->search = $search;
        $this->order = $order;
    }

    public function pagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function search(): ?SearchTerms
    {
        return $this->search;
    }

    public function order(): ?QueryOrder
    {
        return $this->order;
    }
}
