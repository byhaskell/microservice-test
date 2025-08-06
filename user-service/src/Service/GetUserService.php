<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\User;

class GetUserService
{
    protected ?array $users = [];

    public function writeDownUser(): void
    {
        for($i = 1; $i <= 10; $i++) {
            $email = 'test'.$i.'@test.com';
            $this->users[$i] = new User(
                $i,
                'User '.$i,
                $email,
            );
        }
    }

    public function getById($id)
    {
        return $this->users[$id] ?? null;
    }

    public function getAll(): array
    {
        return $this->users;
    }
}
