<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Model\User\Exception;

use AdnanMula\Cards\Domain\Model\Shared\Exception\ExistsException;

final class UserAlreadyExistsException extends ExistsException
{
    public function __construct()
    {
        parent::__construct('User already exists.');
    }
}
