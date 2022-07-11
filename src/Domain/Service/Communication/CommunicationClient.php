<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Communication;

interface CommunicationClient
{
    public function say(string $msg, string $to): void;
}
