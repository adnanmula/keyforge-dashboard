<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Persistence;

interface Fixture
{
    public function load(): void;

    public function isLoaded(): bool;

    /** @return array<string> */
    public function dependants(): array;
}
