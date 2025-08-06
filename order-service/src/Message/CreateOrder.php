<?php
namespace App\Message;

readonly class CreateOrder
{
    public function __construct(
        public array $data,
        public string $queueId
    ) {}
}
