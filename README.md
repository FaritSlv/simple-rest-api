# simple-rest-api

Очень простой REST api.

Клонируем репозиторий. Устанавливаем зависимости:
```bash
composer install
```

Запускаем докер
```bash
docker-compose up -d --build
```

Можно просто запустить через php сервер
```bash
php -S localhost:8000 -t public/
```

Переходим на http://localhost:8000 и там читаем мини-документацию