<?php
declare(strict_types=1);
namespace App\Service;

use App\Model\User;

class CreateUserService
{
    public function create($data): User|array
    {
        $validate = $this->validate($data);
        if (!empty($validate)) {
            return ['errors' => $validate];
        }

        return new User(rand(1,10), $data['name'], $data['email']);
    }

    private function validate($data): array
    {
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'Поле імʼя не заповнено';
        }
        if (empty($data['email'])) {
            $errors[] = 'Поле електронної пошти не заповнено';
        }elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Поле електронної пошти не вірного формата';
        }
        return $errors;
    }
}
