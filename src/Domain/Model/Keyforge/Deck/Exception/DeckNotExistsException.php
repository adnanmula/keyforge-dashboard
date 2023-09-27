<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Keyforge\Deck\Exception;

use AdnanMula\Cards\Domain\Model\Shared\Exception\NotFoundException;

final class DeckNotExistsException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Deck not exists');
    }
}
