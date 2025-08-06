**Запуск Symfony:**

1. make start

2. Відкрити Order Service та запустити воркер RabbitMQ:
    - php bin/console app:order-rpc-server

4. Відкрити User Service та запустити воркер RabbitMQ:
   - php bin/console app:user-rpc-server

3. Запит на створення замовлення (uses test1..10)

``
{
   "user": 1,
   "amount": 100.5,
   "products": [
      {"id": 1,"name":"product","quantity":1}
   ]
}
``

5. make stop