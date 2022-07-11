<?php declare(strict_types=1);

namespace AdnanMula\Cards\Tests\Application\Subscribe;

use AdnanMula\Cards\Application\User\Subscribe\SubscribeUserCommand;
use AdnanMula\Cards\Application\User\Subscribe\SubscribeUserCommandHandler;
use AdnanMula\Cards\Domain\Model\User\Exception\UserAlreadyExistsException;
use AdnanMula\Cards\Domain\Model\User\UserRepository;
use AdnanMula\Cards\Domain\Service\User\UserCreator;
use AdnanMula\Cards\Tests\Mock\Domain\Model\User\UserMotherObject;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SubscribeUserCommandHandlerTest extends TestCase
{
    private MockObject $repository;
    private SubscribeUserCommandHandler $handler;

    /** @test */
    public function given_unsubscribed_user_then_subscribe(): void
    {
        $id = UserMotherObject::MOCK_ID;
        $reference = UserMotherObject::MOCK_REF;
        $name = UserMotherObject::MOCK_NAME;

        $this->repository->expects($this->once())
            ->method('byReference')
            ->with($reference)
            ->willReturn(null);

        $this->repository->expects($this->once())
            ->method('save')
            ->with(UserMotherObject::buildDefault());

        ($this->handler)($this->command($id, $reference, $name));
    }

    /** @test */
    public function given_subscribed_user_then_throw_exception(): void
    {
        $this->expectException(UserAlreadyExistsException::class);

        $id = UserMotherObject::MOCK_ID;
        $reference = UserMotherObject::MOCK_REF;
        $name = UserMotherObject::MOCK_NAME;

        $this->repository->expects($this->once())
            ->method('byReference')
            ->with($reference)
            ->willReturn(UserMotherObject::buildDefault());

        $this->repository->expects($this->never())->method('save');

        ($this->handler)($this->command($id, $reference, $name));
    }

    protected function setUp(): void
    {
        $this->repository = $this->createMock(UserRepository::class);

        $this->handler = new SubscribeUserCommandHandler(
            new UserCreator($this->repository),
        );
    }

    private function command(string $id, string $reference, string $username): SubscribeUserCommand
    {
        return new SubscribeUserCommand($id, $reference, $username);
    }
}
