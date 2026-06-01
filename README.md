# Proxy Manager

Приложение для управления списком прокси-серверов. Позволяет добавлять, редактировать, удалять прокси и отслеживать их статус работоспособности.

## Стек

- **Backend:** PHP 8.3, Laravel 13
- **Frontend:** Vue 3 (Composition API), TypeScript, Pinia, Vue Router
- **Database:** MySQL 8.0
- **API:** REST (JSON)
- **Документация:** Swagger / OpenAPI 3.0

## Быстрый старт

### 1. Запуск контейнеров

```bash
docker-compose up -d --build
```

Будут запущены 4 контейнера:

| Контейнер | Назначение |
|---|---|
| `proxy-manager-mysql` | MySQL 8.0 |
| `proxy-manager-php` | PHP-FPM с Laravel (порт 9000) |
| `proxy-manager-nginx` | Nginx (порт 8081) |
| `proxy-manager-scheduler` | Планировщик задач Laravel |

### 2. Установка зависимостей

```bash
docker exec proxy-manager-php composer install
```

### 3. Настройка окружения

Файл `.env` уже настроен для работы с MySQL в Docker. При необходимости изменить параметры подключения — отредактируйте `.env`:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=proxy_manager
DB_USERNAME=proxy_user
DB_PASSWORD=proxy_pass
```

### 4. Миграции

```bash
docker exec proxy-manager-php php artisan migrate
```

### 5. Запуск фронтенда

```bash
cd frontend
npm install
npx vite
```

## Использование

### API Endpoints

Все запросы принимают и возвращают JSON.

| Метод | URL | Описание |
|---|---|---|
| `GET` | `/api/proxies` | Получить список всех прокси |
| `POST` | `/api/proxies` | Создать новый прокси |
| `GET` | `/api/proxies/{proxy}` | Получить прокси по ID |
| `PUT` | `/api/proxies/{proxy}` | Обновить прокси |
| `DELETE` | `/api/proxies/{proxy}` | Удалить прокси |
| `POST` | `/api/proxies/{proxy}/check` | Проверить статус одного прокси |
| `POST` | `/api/proxies/check-all` | Проверить статус всех прокси |

Пример создания прокси:

```bash
curl -X POST http://localhost:8081/api/proxies \
  -H "Content-Type: application/json" \
  -d '{"ip":"192.168.1.1","port":3128,"type":"http"}'
```

### Доступные серверы

| Сервер | URL | Назначение |
|---|---|---|
| Backend (Nginx) | `http://localhost:8081` | API и статика Laravel |
| Frontend (Vite) | `http://localhost:5173` | SPA с проксированием `/api` на backend |

## Swagger документация

Swagger UI доступен по адресу:

```
http://localhost:8081/api/docs
```

Или через Vite proxy:

```
http://localhost:5173/api/docs
```

JSON-спецификация: `/api/docs/json`.

### Генерация документации

Документация генерируется **автоматически** при каждом запросе `/api/docs/json` — отдельная команда не требуется.

Аннотации расположены в файле:

```
backend/app/Http/Controllers/Docs/Swagger/ProxyController.php
```

## Автоматическая проверка статуса

- **Backend:** Artisan-команда `proxies:check-status` запускается планировщиком Laravel каждые 5 минут внутри контейнера `proxy-manager-scheduler`
- **Frontend:** Автообновление списка каждые 5 минут через `setInterval` в Pinia store

Ручная проверка — кнопка **Check All** в интерфейсе или `POST /api/proxies/check-all`.

## Структура проекта

```
proxy-manager/
├── docker-compose.yml
├── docker/
│   └── nginx/default.conf
├── backend/
│   ├── app/
│   │   ├── Console/Commands/CheckProxiesStatus.php
│   │   ├── Http/Controllers/
│   │   │   ├── Api/ProxyController.php
│   │   │   └── Docs/Swagger/ProxyController.php
│   │   ├── Models/Proxy.php
│   │   └── Services/ProxyCheckService.php
│   ├── config/l5-swagger.php
│   ├── database/migrations/..._create_proxies_table.php
│   ├── routes/api.php
│   └── routes/console.php
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   │   ├── ProxyForm.vue
│   │   │   └── ProxyStatusBadge.vue
│   │   ├── pages/ProxyListPage.vue
│   │   ├── router/index.ts
│   │   ├── stores/proxyStore.ts
│   │   └── types/index.ts
│   └── vite.config.ts
└── README.md
```

## Команды

```bash
# Запустить artisan команду вручную
docker exec proxy-manager-php php artisan proxies:check-status

# Сгенерировать документацию l5-swagger
docker exec proxy-manager-php php artisan l5-swagger:generate

# Просмотр логов планировщика
docker logs proxy-manager-scheduler

# Просмотр логов PHP
docker logs proxy-manager-php
```
