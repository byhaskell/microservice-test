<?php
namespace App\MessageHandler;

use App\Message\CreateOrder;
use App\Message\OrderResponse;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CreateOrderHandler
{
    public function __construct(private MessageBusInterface $bus) {}

    public function __invoke(CreateOrder $message)
    {
        $response = new OrderResponse(
            orderId: rand(1000, 9999),
            data: $message->data,
            status: 'created',
            queueId: $message->queueId,
        );
        $this->bus->dispatch($response);
    }
}
