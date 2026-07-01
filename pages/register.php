<?php
// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = validateInput($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullname = validateInput($_POST['fullname'] ?? '');
    $phone = validateInput($_POST['phone'] ?? '');
    $email = validateInput($_POST['email'] ?? '');
    
    $errors = [];
    
    // Валидация логина (только кириллица, не менее 6 символов)
    if (empty($login)) {
        $errors['login'] = 'Поле логин обязательно для заполнения';
    } elseif (!preg_match('/^[а-яА-ЯёЁ]{6,}$/u', $login)) {
        $errors['login'] = 'Логин должен содержать только кириллицу и быть не менее 6 символов';
    } else {
        // Проверка уникальности логина
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetchColumn() > 0) {
            $errors['login'] = 'Пользователь с таким логином уже существует';
        }
    }
    
    // Валидация пароля (минимум 6 символов)
    if (empty($password)) {
        $errors['password'] = 'Поле пароль обязательно для заполнения';
    } elseif (strlen($password) < 6) {
        $errors['password'] = 'Пароль должен содержать не менее 6 символов';
    }
    
    // Валидация ФИО (символы кириллицы и пробелы, ровно 3 слова)
    if (empty($fullname)) {
        $errors['fullname'] = 'Поле ФИО обязательно для заполнения';
    } elseif (!preg_match('/^[а-яА-ЯёЁ\s]+$/u', $fullname)) {
        $errors['fullname'] = 'ФИО должно содержать только символы кириллицы и пробелы';
    } else {
        // Проверка на ровно 3 слова
        $words = preg_split('/\s+/', trim($fullname));
        if (count($words) != 3) {
            $errors['fullname'] = 'ФИО должно содержать ровно 3 слова (Фамилия Имя Отчество)';
        }
    }
    
    // Валидация телефона (формат +7(XXX)-XXX-XX-XX)
    if (empty($phone)) {
        $errors['phone'] = 'Поле телефон обязательно для заполнения';
    } elseif (!preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['phone'] = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
    }
    
    // Валидация email
    if (empty($email)) {
        $errors['email'] = 'Поле email обязательно для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите корректный адрес электронной почты';
    } else {
        // Проверка уникальности email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $errors['email'] = 'Пользователь с таким email уже существует';
        }
    }
    
    // Если нет ошибок, регистрируем пользователя
    if (empty($errors)) {
        try {
            // Хеширование пароля
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Добавление пользователя в базу данных
            $stmt = $pdo->prepare("INSERT INTO users (login, password, fullname, phone, email) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$login, $hashedPassword, $fullname, $phone, $email]);
            
            // Получение ID нового пользователя
            $userId = $pdo->lastInsertId();
            
            // Автоматическая авторизация
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_login'] = $login;
            
            // Перенаправление на страницу личного кабинета
            header("Location: index.php?page=applications");
            exit();
        } catch (PDOException $e) {
            $registerError = 'Ошибка при регистрации. Пожалуйста, попробуйте позже.';
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
                    <h4><i class="fas fa-user-plus me-2"></i>Регистрация</h4>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($registerError)): ?>
                    <div class="alert alert-danger"><?= $registerError ?></div>
                <?php endif; ?>
                
                <form id="registerForm" method="post" action="index.php?page=register">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">ФИО <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            <input type="text" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>" 
                                   id="fullname" name="fullname" placeholder="Иванов Иван Иванович" value="<?= $_POST['fullname'] ?? '' ?>" required>
                            <?php if (isset($errors['fullname'])): ?>
                                <div class="invalid-feedback"><?= $errors['fullname'] ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($errors['fullname'])): ?>
                            <div class="form-text">Только символы кириллицы, формат: Фамилия Имя Отчество</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Телефон <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                                   id="phone" name="phone" placeholder="+7(XXX)-XXX-XX-XX" value="<?= $_POST['phone'] ?? '' ?>" required>
                            <?php if (isset($errors['phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['phone'] ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($errors['phone'])): ?>
                            <div class="form-text">В формате +7(XXX)-XXX-XX-XX</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Адрес электронной почты <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" placeholder="example@mail.ru" value="<?= $_POST['email'] ?? '' ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control <?= isset($errors['login']) ? 'is-invalid' : '' ?>" 
                                   id="login" name="login" placeholder="логин" value="<?= $_POST['login'] ?? '' ?>" required>
                            <?php if (isset($errors['login'])): ?>
                                <div class="invalid-feedback"><?= $errors['login'] ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($errors['login'])): ?>
                            <div class="form-text">Только кириллица, не менее 6 символов</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                   id="password" name="password" placeholder="••••••" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback"><?= $errors['password'] ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($errors['password'])): ?>
                            <div class="form-text">Не менее 6 символов</div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Зарегистрироваться</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Уже есть аккаунт? <a href="index.php?page=login"><i class="fas fa-sign-in-alt me-1"></i>Войти</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Подключение библиотеки для маски ввода -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    // Инициализация маски для телефона
    $('#phone').mask('+7(000)-000-00-00');
    
    // Проверка формы перед отправкой
    $('#registerForm').submit(function(e) {
        let isValid = true;
        let errorMessages = {};
        
        // Валидация логина (только кириллица, не менее 6 символов)
        const login = $('#login').val();
        // Используем более точную проверку кириллицы
        if (!/^[\u0410-\u044F\u0451\u0401]{6,}$/u.test(login)) {
            $('#login').addClass('is-invalid');
            errorMessages.login = 'Логин должен содержать только кириллицу и быть не менее 6 символов';
            isValid = false;
        } else {
            $('#login').removeClass('is-invalid');
        }
        
        // Валидация ФИО (только кириллица и пробелы, ровно 3 слова)
        const fullname = $('#fullname').val();
        if (!/^[\u0410-\u044F\u0451\u0401\s]+$/u.test(fullname)) {
            $('#fullname').addClass('is-invalid');
            errorMessages.fullname = 'ФИО должно содержать только символы кириллицы и пробелы';
            isValid = false;
        } else {
            // Проверка на ровно 3 слова
            const words = fullname.trim().split(/\s+/);
            if (words.length !== 3) {
                $('#fullname').addClass('is-invalid');
                errorMessages.fullname = 'ФИО должно содержать ровно 3 слова (Фамилия Имя Отчество)';
                isValid = false;
            } else {
                $('#fullname').removeClass('is-invalid');
            }
        }
        
        // Валидация пароля (не менее 6 символов)
        const password = $('#password').val();
        if (password.length < 6) {
            $('#password').addClass('is-invalid');
            errorMessages.password = 'Пароль должен содержать не менее 6 символов';
            isValid = false;
        } else {
            $('#password').removeClass('is-invalid');
        }
        
        // Валидация телефона
        const phone = $('#phone').val();
        if (!/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/.test(phone)) {
            $('#phone').addClass('is-invalid');
            errorMessages.phone = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
            isValid = false;
        } else {
            $('#phone').removeClass('is-invalid');
        }
        
        // Если форма невалидна, отменяем отправку и показываем ошибки
        if (!isValid) {
            e.preventDefault();
            
            // Очищаем старые сообщения об ошибках
            $('.invalid-feedback').remove();
            
            // Отображаем сообщения об ошибках
            for (const field in errorMessages) {
                $(`#${field}`).after(`<div class="invalid-feedback" style="display: block;">${errorMessages[field]}</div>`);
            }
        }
    });
    
    // Дополнительная валидация при вводе
    $('#login').on('input', function() {
        const value = $(this).val();
        if (value && !/^[\u0410-\u044F\u0451\u0401]+$/u.test(value)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback" style="display: block;">Логин должен содержать только кириллицу</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
    
    $('#fullname').on('input', function() {
        const value = $(this).val();
        if (value && !/^[\u0410-\u044F\u0451\u0401\s]+$/u.test(value)) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback" style="display: block;">ФИО должно содержать только символы кириллицы и пробелы</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
</script> 