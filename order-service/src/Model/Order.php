<?php
declare(strict_types=1);

namespace App\Model;

class Order
{
    public int $id;

    public string $status;

    public float $amount;

    public array $products;

    public array $user;

    public function __construct(int $id, string $status, float  $amount, array $products, array $user)
    {
        $this->id = $id;
        $this->status = $status;
        $this->amount = $amount;
        $this->products = $products;
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    public function setProducts(array $products): void
    {
        $this->products = $products;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }
}
