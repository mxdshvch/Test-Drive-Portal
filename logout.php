<?php
session_start();

// Очистка всех данных сессии
$_SESSION = array();

// Уничтожение сессии
session_destroy();

// Получаем URL для возврата (если указан)
$returnUrl = isset($_GET['return']) ? $_GET['return'] : '';

// Проверяем, является ли возвращаемый URL допустимым
$allowedReturns = ['landing.php'];
if (in_array($returnUrl, $allowedReturns)) {
    // Если URL допустимый, перенаправляем на него
    header("Location: " . $returnUrl);
} else {
    // Иначе перенаправляем на страницу входа
    header("Location: index.php?page=login");
}
exit();
?> 