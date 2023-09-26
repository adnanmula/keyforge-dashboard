<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\Shared\Exception;

final class UserNotExistsException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('User not exists');
    }
}
