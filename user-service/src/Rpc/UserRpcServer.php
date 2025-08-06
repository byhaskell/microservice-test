<?php
namespace App\Rpc;

use App\Service\CreateUserService;
use App\Service\GetUserService;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class UserRpcServer
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private GetUserService $getUserService,
        private CreateUserService $createUserService,
    ) {}

    /**
     * @throws \Exception
     */
    public function start(): void
    {
        // Підключаємо до RabbitMQ
        $connection = new AMQPStreamConnection(
            $this->parameterBag->get('rabbitmq_host'),
            $this->parameterBag->get('rabbitmq_port'),
            $this->parameterBag->get('rabbitmq_user'),
            $this->parameterBag->get('rabbitmq_password')
        );

        // Канал для обміну повідомленнями
        /** @var AMQPChannel $channel */
        $channel = $connection->channel();

        // Створюємо чергу, якщо не існує
        $channel->queue_declare('user_rpc', false, false, false, false);

        // Обмежуємо одночасну обробку до 1
        $channel->basic_qos(0, 1, false);

        echo "⌛ Очікуємо RPC запити по користувачам...\n";

        // Читаємо повідомлення з каналу по черзі user_rpc
        $channel->basic_consume('user_rpc', '', false, false, false, false,
            function (AMQPMessage $req) use ($channel) {

                $data = json_decode($req->getBody(), true);

                // Дія створення або отримання даних
                $action = $data['action'] ?? '';
                $payload = $data['payload'] ?? [];

                $response = $this->getResponse($action, $payload);

                // Формуємо повідомлення у відповідь
                $msg = new AMQPMessage(json_encode($response), [
                    'correlation_id' => $req->get('correlation_id'),
                ]);

                // Відправляємо у чергу (яка вказана в reply_to)
                $channel->basic_publish($msg, '', $req->get('reply_to'));

                // І тільки зараз підтверджуємо обробку повідомлення яке отримали
                $channel->basic_ack($req->get('delivery_tag'));

                if ($action === 'get') {
                    echo "✅ Отримано дані про користувача...\n";
                }
                if ($action === 'create') {
                    echo "✅ Створили користувача...\n";
                }
            }
        );

        // "вічний" цикл очікування повідомлень, при помилці - завершуємо роботу!
        while ($channel->is_consuming()) {
            try {
                $channel->wait();
            } catch (\Throwable $e) {
                echo "❌ Помилка: " . $e->getMessage() . PHP_EOL;
                break;
            }
        }

        // Закриваємо канал та зʼєднання, щоб процеси не висіли після завершення роботи при помилці
        $channel->close();
        $connection->close();
    }

    private function getResponse($action, $payload): \App\Model\User|array|null
    {
        $actions = ['get', 'create'];
        if (!in_array($action, $actions)) {
            return ['errors' => ['Не відома дія']];
        }

        if ($action === 'get') {
            // мок, заповнення попередніми даними (користувачами)
            $this->getUserService->writeDownUser();
            $user = $this->getUserService->getById((int)$payload['userId'] ?? 0);
            if (empty($user)) {
                return ['errors' => ['Користувача не знайдено']];
            }
            return $user;
        }

        if ($action === 'create') {
            return $this->createUserService->create($payload);
        }

        return null;
    }
}
