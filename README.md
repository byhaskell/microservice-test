**Запуск Symfony:**

1. make start

2. Відкрити Order Service та запустити воркер RabbitMQ:
    - php bin/console app:order-rpc-server

3. Відкрити User Service та запустити воркер RabbitMQ:
   - php bin/console app:user-rpc-server

4. Запит на створення замовлення (uses test1..10)

         http://localhost/orders
         {
             "user": 1,
             "amount": 100.5,
             "products": [
                {"id": 1,"name":"product","quantity":1}
             ]
         }

5. Перевірка отримання даних користувача через http:

         http://localhost:8080/api/v1/user/100
         headers: "X-API-Key": "1111"

6. Перевірка отримання даих користувача через curl із Api Gateway:

         curl --location --request GET 'http://nginx-user-service:8080/api/v1/user/1' \
         --header 'X-API-Key: 1111' \
         --header 'Accept: application/json, text/plain, */*'

7. Створення користувача через http:

       http://localhost:8080/api/v1/user/create
       {
           "name": "User",
           "email":"test@test.com"
       }

8. Створення користувача через curl із Api Gateway:

         curl --location --request POST 'http://nginx-user-service:8080/api/v1/user/create' \
         --header 'X-API-Key: 1111' \
         --header 'Accept: application/json, text/plain, */*' \
         --header 'Content-Type: application/json;charset=utf-8' \
         --data-raw '{"name": "User","email":"test@test.com"}'

9. make stop
