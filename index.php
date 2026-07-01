<?php
session_start();
require_once 'config.php';

// Проверка авторизации
$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();

// Определение страницы для отображения
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// Если пользователь авторизован, перенаправляем на страницу заявок
if ($isLoggedIn && $page == 'login') {
    $page = 'applications';
}

// Если пользователь администратор, перенаправляем в панель администратора
if ($isAdmin && $page == 'applications') {
    $page = 'admin';
}

// Если пользователь не авторизован, ограничиваем доступ только к страницам логина и регистрации
if (!$isLoggedIn && !in_array($page, ['login', 'register'])) {
    $page = 'login';
}

// Если пользователь не администратор, запрещаем доступ к админке
if (!$isAdmin && $page == 'admin') {
    $page = 'applications';
}

// Определение доступных страниц
$availablePages = [
    'login', 'register', 'applications', 'new_application', 'admin'
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тест-драйв премиальных автомобилей</title>
    <!-- SF Font CSS -->
    <link href="css/sf-font.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Верхняя навигационная панель -->
        <nav class="navbar navbar-dark bg-dark-blue">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <img src="Logo.svg" alt="Логотип" class="logo me-2">
                </div>
                <div class="d-flex">
                    <a href="landing.php" class="btn btn-outline-light btn-sm me-2"><i class="fas fa-home me-1"></i>О нас</a>
                    <?php if ($isLoggedIn): ?>
                        <a href="logout.php" class="btn btn-warning btn-sm"><i class="fas fa-sign-out-alt me-1"></i>Выйти</a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-warning btn-sm"><i class="fas fa-sign-in-alt me-1"></i>Войти</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <!-- Основное содержимое -->
        <div class="container mt-4">
            <?php
            // Подключение соответствующей страницы
            switch ($page) {
                case 'register':
                    include 'pages/register.php';
                    break;
                case 'login':
                    include 'pages/login.php';
                    break;
                case 'applications':
                    include 'pages/applications.php';
                    break;
                case 'new_application':
                    include 'pages/new_application.php';
                    break;
                case 'admin':
                    include 'pages/admin.php';
                    break;
                default:
                    include 'pages/login.php';
            }
            ?>
        </div>
    </div>

    <!-- jQuery (необходим для Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS и зависимости -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
    <?php if ($page === 'admin'): ?>
    <!-- Admin JS -->
    <script src="js/admin.js"></script>
    <?php endif; ?>
</body>
</html> 