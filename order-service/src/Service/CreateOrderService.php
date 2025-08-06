<?php
namespace App\Service;

use App\Model\Order;
use App\Rpc\UserRpcClient;

class CreateOrderService
{
    public function __construct(private readonly UserRpcClient $userRpcClient) {}

    public function create($data): Order|array
    {
        // Щоб мінімізувати запити до user.rpc
        // заміняємо data на той що був при валідації
        // якщо все ок, то замість обʼєкта user буде новий з його ID
        [$validate, $data] = $this->validate($data);
        if (!empty($validate)) {
            return ['errors' => $validate];
        }

        return new Order(
            id: rand(1, 10),
            status: 'created',
            amount: $data['amount'],
            products: $data['products'],
            user: $data['user'],
        );
    }

    private function validate($data): array
    {
        $errors = [];
        if (empty($data['user'])) {
            $errors[] = 'Не заповнені дані про користувача';
        } elseif (intval($data['user']) <= 0) {
            $errors[] = 'Користувач не знайдено';
        }
        if (empty($data['amount'])) {
            $errors[] = 'Поле amount не заповнено';
        }
        if (empty($data['products'])) {
            $errors[] = 'Товари не обрано';
        }
        if (!empty($data['products'])) {
            foreach ($data['products'] as $product) {
                if (empty($product['id'])) {
                    $errors[] = printf('%s %d', 'Невідомий товар №', $product['id']);
                }
                if (empty($product['quantity'])) {
                    $errors[] = printf('%s %d', 'Невідома кількість товару №', $product['id']);
                } elseif (intval($product['quantity']) <= 0) {
                    $errors[] = printf('%s %d', 'Невірна кількість товару №', $product['id']);
                }
                if (empty($product['name'])) {
                    $errors[] = printf('%s %d', 'Відсутня назва товару №', $product['id']);
                }
            }
        }

        // Щоб мінімізувати навантаження, якщо помилок немає, то тільки тоді пробуємо отримати дані про користувача
        if (empty($errors)) {
            $user = $this->userRpcClient->call([
                'action' => 'get',
                'payload' => ['userId' => $data['user']],
            ]);
            if (empty($user['id'])) {
                $errors[] = 'Користувача з таким email не знайдено';
            } else {
                $data['user'] = $user;
            }
        }
        return [$errors, $data];
    }
}
