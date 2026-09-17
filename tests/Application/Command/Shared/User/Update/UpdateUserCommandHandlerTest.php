<?php declare(strict_types=1);

namespace AdnanMula\Cards\Tests\Application\Command\Shared\User\Update;

use AdnanMula\Cards\Application\Command\Shared\User\Update\UpdateUserCommand;
use AdnanMula\Cards\Application\Command\Shared\User\Update\UpdateUserCommandHandler;
use AdnanMula\Cards\Domain\Model\Shared\UserRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UpdateUserCommandHandlerTest extends TestCase
{
    private MockObject&UserRepository $repository;
    private MockObject&UserPasswordHasherInterface $hasher;
    private UpdateUserCommandHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);
        $this->hasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->handler = new UpdateUserCommandHandler(
            $this->repository,
            $this->hasher,
        );
    }

    public function testPepe(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $this->repository->expects(self::once())
            ->method('byId')
            ->willReturn(null);

        $this->hasher->expects(self::never())->method('hashPassword');

        ($this->handler)(new UpdateUserCommand('3b2cde45-c647-48a6-bd7b-2be10ed89a88', 'pass', 'en_GB', 'a', 'b'));
    }
}
