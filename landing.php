<?php
session_start();
require_once 'config.php';

// Проверка авторизации
$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();
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
    <link href="css/landing.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-page">
    <!-- Навигационная панель -->
    <nav class="navbar navbar-dark bg-dark-blue sticky-top">
        <div class="container">
            <div class="d-flex align-items-center">
                <img src="Logo.svg" alt="Логотип" class="logo me-2">
            </div>
            <div class="d-flex">
                <?php if ($isLoggedIn): ?>
                    <a href="index.php?page=applications" class="btn btn-outline-light me-2"><i class="fas fa-user me-2"></i>Личный кабинет</a>
                    <a href="logout.php?return=landing.php" class="btn btn-warning"><i class="fas fa-sign-out-alt me-2"></i>Выйти</a>
                <?php else: ?>
                    <a href="index.php?page=login" class="btn btn-outline-light me-2"><i class="fas fa-sign-in-alt me-2"></i>Войти</a>
                    <a href="index.php?page=register" class="btn btn-warning"><i class="fas fa-user-plus me-2"></i>Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Главная секция -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 hero-content">
                    <h1 class="display-3 fw-bold slide-up">Испытайте<br>ощущение<br>скорости</h1>
                    <p class="lead mt-4 mb-5 slide-up delay-1">Забронируйте тест-драйв премиального автомобиля прямо сейчас и получите незабываемые впечатления от вождения</p>
                    <div class="slide-up delay-2">
                        <?php if ($isLoggedIn): ?>
                            <a href="index.php?page=new_application" class="btn btn-warning btn-lg me-3"><i class="fas fa-car me-2"></i>Забронировать тест-драйв</a>
                        <?php else: ?>
                            <a href="index.php?page=register" class="btn btn-warning btn-lg me-3"><i class="fas fa-car me-2"></i>Забронировать тест-драйв</a>
                        <?php endif; ?>
                        <a href="#how-it-works" class="btn btn-outline-dark btn-lg"><i class="fas fa-info-circle me-2"></i>Узнать больше</a>
                    </div>
                </div>
                <div class="col-lg-6 hero-image">
                    <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&q=85&fm=jpg&crop=entropy&cs=srgb&w=600" alt="Премиальный автомобиль" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Преимущества -->
    <section class="features py-6 bg-light-gray">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Почему выбирают нас</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-car-side"></i>
                        </div>
                        <h3 class="fw-bold">Премиальные автомобили</h3>
                        <p>Широкий выбор luxury автомобилей от ведущих мировых брендов в идеальном техническом состоянии</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3 class="fw-bold">Гибкое расписание</h3>
                        <p>Выбирайте удобное для вас время и место проведения тест-драйва в любой день недели</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="fw-bold">Полная безопасность</h3>
                        <p>Все автомобили застрахованы, а тест-драйвы проходят под наблюдением опытных инструкторов</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Доступные модели -->
    <section class="models py-6">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Наши премиальные модели</h2>
            
            <div class="model-tabs">
                <ul class="nav nav-pills justify-content-center mb-4" id="brandTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="bmw-tab" data-bs-toggle="pill" data-bs-target="#bmw" type="button">BMW</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="mercedes-tab" data-bs-toggle="pill" data-bs-target="#mercedes" type="button">Mercedes-Benz</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="audi-tab" data-bs-toggle="pill" data-bs-target="#audi" type="button">Audi</button>
                    </li>
                </ul>
                
                <div class="tab-content" id="brandTabsContent">
                    <div class="tab-pane fade show active" id="bmw" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/BMW X5.jpg" alt="BMW X5" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">BMW X5</h4>
                                    <p>Мощный и комфортный премиальный внедорожник с характерным дизайном BMW.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=1&model=1" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/BMW 5 series.jpg" alt="BMW 5 Series" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">BMW 5 Series</h4>
                                    <p>Элегантный бизнес-седан с передовыми технологиями и динамическими характеристиками.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=1&model=2" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/BMW 7 Series.jpg" alt="BMW 7 Series" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">BMW 7 Series</h4>
                                    <p>Флагманский седан с непревзойденным комфортом и роскошным интерьером.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=1&model=3" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="mercedes" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Mercedes GLE.jpg" alt="Mercedes GLE" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Mercedes GLE</h4>
                                    <p>Премиальный кроссовер с выдающимся комфортом и современными технологиями.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=2&model=4" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Mercedes E-Class.jpg" alt="Mercedes E-Class" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Mercedes E-Class</h4>
                                    <p>Элегантный бизнес-седан с непревзойденным качеством отделки и плавностью хода.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=2&model=5" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Mercedes S-Class.jpg" alt="Mercedes S-Class" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Mercedes S-Class</h4>
                                    <p>Роскошный флагманский седан с инновационными технологиями и первоклассным комфортом.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=2&model=6" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="audi" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Audi Q5.jpg" alt="Audi Q5" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Audi Q5</h4>
                                    <p>Элегантный среднеразмерный внедорожник с современными технологиями и утонченным дизайном.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=3&model=7" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Audi A6.jpg" alt="Audi A6" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Audi A6</h4>
                                    <p>Представительский седан с передовыми технологиями и характерным стильным дизайном Audi.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=3&model=8" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="model-card">
                                    <div class="model-image rounded-4 overflow-hidden">
                                        <img src="landing photo/Audi A8.jpg" alt="Audi A8" class="img-fluid">
                                    </div>
                                    <h4 class="fw-bold mt-3">Audi A8</h4>
                                    <p>Флагманский седан Audi с роскошным интерьером и инновационными технологиями.</p>
                                    <?php if ($isLoggedIn): ?>
                                        <a href="index.php?page=new_application&brand=3&model=9" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php else: ?>
                                        <a href="index.php?page=register" class="btn btn-outline-dark"><i class="fas fa-calendar-check me-2"></i>Забронировать</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Как это работает -->
    <section class="how-it-works py-6 bg-light-gray" id="how-it-works">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Как это работает</h2>
            
            <div class="steps">
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="step-card">
                            <div class="step-number">1</div>
                            <h4 class="fw-bold">Регистрация</h4>
                            <p>Создайте аккаунт на нашем сайте, указав необходимые данные.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-card">
                            <div class="step-number">2</div>
                            <h4 class="fw-bold">Выберите автомобиль</h4>
                            <p>Выберите модель автомобиля из нашего премиального автопарка.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-card">
                            <div class="step-number">3</div>
                            <h4 class="fw-bold">Забронируйте время</h4>
                            <p>Выберите удобную дату и время для тест-драйва.</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="step-card">
                            <div class="step-number">4</div>
                            <h4 class="fw-bold">Наслаждайтесь поездкой</h4>
                            <p>Приезжайте в назначенное время и получайте незабываемые эмоции от вождения.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Отзывы -->
    <section class="testimonials py-6">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Отзывы наших клиентов</h2>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Великолепный сервис! Очень удобно записаться на тест-драйв онлайн. BMW M5 Competition оставил незабываемые впечатления."</p>
                        <div class="testimonial-author">
                            <div class="author-name">Алексей К.</div>
                            <div class="author-info">Тест-драйв BMW M5</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Отличный опыт! Автомобиль был в идеальном состоянии, менеджеры очень профессиональны. Обязательно приду еще."</p>
                        <div class="testimonial-author">
                            <div class="author-name">Екатерина Л.</div>
                            <div class="author-info">Тест-драйв Mercedes GLE</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <p class="testimonial-text">"Шикарная возможность попробовать автомобиль мечты перед покупкой. Очень удобный сервис и отзывчивый персонал."</p>
                        <div class="testimonial-author">
                            <div class="author-name">Дмитрий В.</div>
                            <div class="author-info">Тест-драйв Audi Q7</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA секция -->
    <section class="cta py-6 bg-dark-blue">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 text-center text-lg-start">
                    <h2 class="text-white fw-bold">Готовы испытать премиальный автомобиль?</h2>
                    <p class="text-white opacity-75 mb-4 mb-lg-0">Зарегистрируйтесь сейчас и получите возможность забронировать тест-драйв.</p>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <?php if ($isLoggedIn): ?>
                        <a href="index.php?page=new_application" class="btn btn-warning btn-lg"><i class="fas fa-car me-2"></i>Забронировать тест-драйв</a>
                    <?php else: ?>
                        <a href="index.php?page=register" class="btn btn-warning btn-lg"><i class="fas fa-car me-2"></i>Забронировать тест-драйв</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer class="footer py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center mb-3">
                        <img src="Logo.svg" alt="Логотип" class="logo me-2">
                    </div>
                    <p class="text-muted">Премиальные тест-драйвы для истинных ценителей автомобилей.</p>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Навигация</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-muted">Главная</a></li>
                        <li><a href="#how-it-works" class="text-decoration-none text-muted">Как это работает</a></li>
                        <li><a href="index.php?page=register" class="text-decoration-none text-muted">Регистрация</a></li>
                        <li><a href="index.php?page=login" class="text-decoration-none text-muted">Вход</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3">Автомобили</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-decoration-none text-muted">BMW</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Mercedes-Benz</a></li>
                        <li><a href="#" class="text-decoration-none text-muted">Audi</a></li>
                        
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold mb-3">Контакты</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-phone me-2 text-muted"></i> +7 (XXX) XXX-XX-XX</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2 text-muted"></i> info@testdrive.ru</li>
                        <li><i class="fas fa-map-marker-alt me-2 text-muted"></i> г. Тюмень, ул. Энергетиков, 45</li>
                    </ul>
                </div>
            </div>
            <div class="border-top mt-4 pt-4 text-center">
                <p class="text-muted mb-0">&copy; 2025 Тест-драйв премиальных автомобилей. Все права защищены.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS и зависимости -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="js/script.js"></script>
</body>
</html> 