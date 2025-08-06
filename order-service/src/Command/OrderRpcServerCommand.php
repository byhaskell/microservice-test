<?php
namespace App\Command;

use App\Rpc\OrderRpcServer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'app:order-rpc-server')]
readonly class OrderRpcServerCommand
{
    public function __construct(private OrderRpcServer $orderRpcServer) {}

    public function __invoke(): int
    {
        $this->orderRpcServer->start();

        return Command::SUCCESS;
    }
}
