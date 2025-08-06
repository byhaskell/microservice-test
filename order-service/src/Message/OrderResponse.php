<?php
namespace App\Message;

class OrderResponse
{
    public function __construct(
        public int $orderId,
        public array $data,
        public string $status,
        public string $queueId,
    ) {}
}
