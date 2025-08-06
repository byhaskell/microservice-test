<?php
namespace App\Service;

use App\Message\CreateOrder;
use App\Message\OrderResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

class OrderRpcClient
{
    public function __construct(
        private MessageBusInterface $bus,
        private TransportInterface $orderResponseTransport
    ) {}

    public function send(CreateOrder $message, int $timeoutMs = 5000): mixed
    {
        $this->bus->dispatch($message);

        $receiver = $this->orderResponseTransport;
        $start = microtime(true);

        do {
            foreach ($receiver->get() as $envelope) {
                /** @var OrderResponse $responseMessage */
                $responseMessage = $envelope->getMessage();
                if ($responseMessage->queueId === $message->queueId) {
                    $receiver->ack($envelope);
                    return $responseMessage;
                }
            }

            usleep(100_000); // 100ms

        } while ((microtime(true) - $start) * 1000 < $timeoutMs);

        return null;
    }
}
