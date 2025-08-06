<?php
namespace App\Rpc;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserRpcClient
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private ?string $callbackQueue;
    private ?string $response;
    private ?string $correlationId;

    /**
     * @throws \Exception
     */
    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
        $this->connection = new AMQPStreamConnection(
            $this->parameterBag->get('rabbitmq_host'),
            $this->parameterBag->get('rabbitmq_port'),
            $this->parameterBag->get('rabbitmq_user'),
            $this->parameterBag->get('rabbitmq_password')
        );
        $this->channel = $this->connection->channel();

        list($this->callbackQueue, ,) = $this->channel->queue_declare("", false, false, true, true);

        $this->channel->basic_consume(
            $this->callbackQueue,
            '',
            false,
            true,
            false,
            false,
            [$this, 'onResponse']
        );
    }

    public function onResponse(AMQPMessage $rep): void
    {
        if ($rep->get('correlation_id') === $this->correlationId) {
            $this->response = $rep->getBody();
        }
    }

    public function call(array $payload)
    {
        $this->response = null;
        $this->correlationId = uniqid();

        $msg = new AMQPMessage(
            json_encode($payload),
            [
                'correlation_id' => $this->correlationId,
                'reply_to' => $this->callbackQueue
            ]
        );

        $this->channel->basic_publish($msg, '', 'user_rpc');

        while (!$this->response) {
            $this->channel->wait(); // Блокуємо до відповіді
        }

        return json_decode($this->response, true);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
