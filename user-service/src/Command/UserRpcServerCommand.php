<?php
namespace App\Command;

use App\Rpc\UserRpcServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'app:user-rpc-server')]
readonly class UserRpcServerCommand
{
    public function __construct(private UserRpcServer $userRpcServer) {}

    public function __invoke(): int
    {
        $this->userRpcServer->start();

        return Command::SUCCESS;
    }
}
