<?php
// Проверка, если пользователь уже авторизован
if (isLoggedIn()) {
    header("Location: index.php?page=applications");
    exit();
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = validateInput($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    // Проверка заполнения полей
    if (empty($login)) {
        $errors['login'] = 'Введите логин';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Введите пароль';
    }
    
    // Если нет ошибок валидации, проверяем учетные данные
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id, login, password FROM users WHERE login = ?");
            $stmt->execute([$login]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Авторизация успешна
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_login'] = $user['login'];
                
                // Перенаправление на страницу заявок
                header("Location: index.php?page=applications");
                exit();
            } else {
                // Неверные учетные данные
                $loginError = 'Некорректные данные';
            }
        } catch (PDOException $e) {
            $loginError = 'Ошибка при входе. Пожалуйста, попробуйте позже.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-sm-12">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="Logo.svg" alt="Логотип" class="logo me-2">
                    <h4><i class="fas fa-sign-in-alt me-2"></i>Вход</h4>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Регистрация успешно завершена. Теперь вы можете войти.</div>
                <?php endif; ?>
                
                <?php if (isset($loginError)): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $loginError ?></div>
                <?php endif; ?>
                
                <form id="loginForm" method="post" action="index.php?page=login">
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>" 
                                   id="login" name="login" placeholder="Введите логин" value="<?= $_POST['login'] ?? '' ?>">
                            <?php if (isset($errors['login'])): ?>
                                <div class="invalid-feedback"><?= $errors['login'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   id="password" name="password" placeholder="Введите пароль">
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt me-2"></i>Войти</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Нет аккаунта? <a href="index.php?page=register"></i>Зарегистрироваться</a></p>
                </div>
            </div>
        </div>
    </div>
</div> 