# Test Drive Portal

Портал заявок на тест-драйв премиальных автомобилей. Пользователи регистрируются, оформляют заявку на выбранную модель; администратор управляет статусами заявок.

## Возможности

- регистрация и авторизация пользователей
- лендинг с описанием сервиса
- создание заявок на тест-драйв
- личный кабинет со списком заявок
- админ-панель со сменой статусов и причиной отклонения

## Стек

- PHP 7.4+
- MySQL
- PDO
- Bootstrap 5
- HTML, CSS, JavaScript

## Быстрый старт

```bash
git clone https://github.com/mxdshvch/Test-Drive-Portal.git
cd Test-Drive-Portal

cp .env.example .env
# укажите параметры MySQL в .env

mysql -u root -p < database/schema.sql
```

Откройте проект через Apache/Nginx или встроенный PHP-сервер:

```bash
php -S 127.0.0.1:8080
```

- Лендинг: http://127.0.0.1:8080/landing.php
- Кабинет: http://127.0.0.1:8080/

## Переменные окружения

| Переменная | Описание |
|------------|----------|
| `DB_HOST` | хост MySQL |
| `DB_PORT` | порт MySQL |
| `DB_NAME` | имя базы |
| `DB_USER` | пользователь MySQL |
| `DB_PASS` | пароль MySQL |
| `ADMIN_LOGIN` | логин администратора из БД |

## Демо-доступ (локально)

После импорта `database/schema.sql` доступен демо-администратор:

- логин: `avto2024`
- пароль: `poehali`

Смените пароль и `ADMIN_LOGIN` перед публикацией в production.

## Структура

```
test-drive-portal/
├── actions/          # API-обработчики
├── css/
├── database/         # schema.sql
├── img/
├── js/
├── pages/            # страницы приложения
├── config.php
├── index.php
├── landing.php
└── logout.php
```

## Автор

mxdshvch
