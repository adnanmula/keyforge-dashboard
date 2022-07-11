<?php declare(strict_types=1);

namespace AdnanMula\Cards\Domain\Service\Persistence;

interface Migration
{
    public function up(): void;
    public function down(): void;
}
