<?php declare(strict_types=1);

namespace AdnanMula\Cards\Application\User\Unsubscribe;

use AdnanMula\Cards\Domain\Model\User\ValueObject\UserReference;
use Assert\Assert;

final class UnsubscribeUserCommand
{
    public const COMMAND = ['/unsubscribe', '/unsub'];

    private UserReference $reference;

    public function __construct($reference)
    {
        Assert::lazy()
            ->that($reference, 'reference')->string()->notBlank()
            ->verifyNow();

        $this->reference = UserReference::from($reference);
    }

    public function reference(): UserReference
    {
        return $this->reference;
    }
}
