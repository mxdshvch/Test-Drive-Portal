<?php
// Проверка авторизации
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit();
}

// Получение ID пользователя
$userId = $_SESSION['user_id'];

// Получение данных пользователя
try {
    $stmt = $pdo->prepare("SELECT fullname, phone FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $error = 'Ошибка при получении данных пользователя: ' . $e->getMessage();
}

// Определение доступных марок автомобилей (только BMW, Mercedes-Benz, Audi)
$availableBrands = [
    ['id' => 1, 'brand_name' => 'BMW'],
    ['id' => 2, 'brand_name' => 'Mercedes-Benz'],
    ['id' => 3, 'brand_name' => 'Audi']
];

// Предварительная загрузка моделей для каждой марки
$carModels = [];

try {
    foreach ($availableBrands as $brand) {
        $stmt = $pdo->prepare("SELECT id, model_name FROM car_models WHERE brand_id = ?");
        $stmt->execute([$brand['id']]);
        $carModels[$brand['id']] = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $error = 'Ошибка при получении моделей автомобилей: ' . $e->getMessage();
}

// Получение выбранной марки и модели из POST или GET данных
$selectedBrandId = $_POST['car_brand'] ?? $_GET['brand'] ?? '';
$selectedModelId = $_POST['car_model'] ?? $_GET['model'] ?? '';

// Если переданы параметры brand и model в URL, делаем их активными
if (!empty($_GET['brand']) && !empty($_GET['model'])) {
    $brandId = (int)$_GET['brand'];
    $modelId = (int)$_GET['model'];
    
    // Проверяем, что такая марка существует в нашем списке
    $brandExists = false;
    foreach ($availableBrands as $brand) {
        if ($brand['id'] == $brandId) {
            $brandExists = true;
            break;
        }
    }
    
    if ($brandExists) {
        $selectedBrandId = $brandId;
        $selectedModelId = $modelId;
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = validateInput($_POST['address'] ?? '');
    $phone = validateInput($_POST['contact_phone'] ?? '');
    $driverLicense = validateInput($_POST['driver_license'] ?? '');
    $licenseDate = $_POST['license_date'] ?? '';
    $carModelId = (int)($_POST['car_model'] ?? 0);
    $desiredDate = $_POST['desired_date'] ?? '';
    $desiredTime = $_POST['desired_time'] ?? '';
    $paymentType = $_POST['payment_type'] ?? '';
    $agreement = isset($_POST['agreement']) ? true : false;
    
    $errors = [];
    
    // Валидация полей
    if (empty($address)) {
        $errors['address'] = 'Введите адрес';
    }
    
    if (empty($phone) || !preg_match('/^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/', $phone)) {
        $errors['contact_phone'] = 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX';
    }
    
    if (empty($driverLicense)) {
        $errors['driver_license'] = 'Введите номер водительского удостоверения';
    }
    
    if (empty($licenseDate)) {
        $errors['license_date'] = 'Введите дату выдачи водительского удостоверения';
    }
    
    if ($carModelId <= 0) {
        $errors['car_model'] = 'Выберите модель автомобиля';
    }
    
    if (empty($desiredDate)) {
        $errors['desired_date'] = 'Выберите желаемую дату';
    }
    
    if (empty($desiredTime)) {
        $errors['desired_time'] = 'Выберите желаемое время';
    }
    
    if (empty($paymentType)) {
        $errors['payment_type'] = 'Выберите тип оплаты';
    }
    
    if (!$agreement) {
        $errors['agreement'] = 'Необходимо согласиться с правилами';
    }
    
    // Если нет ошибок, сохраняем заявку
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO applications 
                (user_id, address, phone, driver_license, license_issue_date, car_model_id, 
                desired_date, desired_time, payment_type, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $userId, $address, $phone, $driverLicense, $licenseDate, $carModelId, 
                $desiredDate, $desiredTime, $paymentType
            ]);
            
            // Перенаправление на страницу заявок с сообщением об успехе
            header("Location: index.php?page=applications&success=1");
            exit();
        } catch (PDOException $e) {
            $applicationError = 'Ошибка при создании заявки: ' . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-sm-12">
        <div class="card shadow-sm">
            <div class="card-header text-center">
                <div class="d-flex justify-content-center align-items-center">
                    <img src="Logo.svg" alt="Логотип" class="logo me-2">
                    <h4><i class="fas fa-car me-2"></i>Новая заявка</h4>
                </div>
            </div>
            <div class="card-body">
                <?php if (isset($applicationError)): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $applicationError ?></div>
                <?php endif; ?>
                
                <form id="applicationForm" method="post" action="index.php?page=new_application">
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                   id="address" name="address" placeholder="Введите адрес" value="<?= $_POST['address'] ?? '' ?>">
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback"><?= $errors['address'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_phone" class="form-label">Контактные данные</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="tel" class="form-control <?= isset($errors['contact_phone']) ? 'is-invalid' : '' ?>" 
                                   id="contact_phone" name="contact_phone" placeholder="+7(XXX)-XXX-XX-XX" 
                                   value="<?= $_POST['contact_phone'] ?? ($user['phone'] ?? '') ?>">
                            <?php if (isset($errors['contact_phone'])): ?>
                                <div class="invalid-feedback"><?= $errors['contact_phone'] ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="driver_license" class="form-label">Водительское удостоверение</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="text" class="form-control <?= isset($errors['driver_license']) ? 'is-invalid' : '' ?>" 
                                           id="driver_license" name="driver_license" placeholder="Номер ВУ" value="<?= $_POST['driver_license'] ?? '' ?>">
                                    <?php if (isset($errors['driver_license'])): ?>
                                        <div class="invalid-feedback"><?= $errors['driver_license'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="license_date" class="form-label">Дата выдачи</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <input type="date" class="form-control <?= isset($errors['license_date']) ? 'is-invalid' : '' ?>" 
                                           id="license_date" name="license_date" value="<?= $_POST['license_date'] ?? '' ?>">
                                    <?php if (isset($errors['license_date'])): ?>
                                        <div class="invalid-feedback"><?= $errors['license_date'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Блок выбора автомобиля с вкладками для каждой марки -->
                    <div class="mb-4">
                        <label class="form-label">Выберите автомобиль</label>
                        
                        <div class="car-selection-tabs">
                            <ul class="nav nav-tabs mb-3" id="carBrandTabs" role="tablist">
                                <?php foreach ($availableBrands as $index => $brand): ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link <?= ($index === 0 && !$selectedBrandId) || $selectedBrandId == $brand['id'] ? 'active' : '' ?>" 
                                            id="brand-tab-<?= $brand['id'] ?>" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#brand-content-<?= $brand['id'] ?>" 
                                            type="button" 
                                            role="tab" 
                                            aria-controls="brand-content-<?= $brand['id'] ?>" 
                                            aria-selected="<?= ($index === 0 && !$selectedBrandId) || $selectedBrandId == $brand['id'] ? 'true' : 'false' ?>"
                                            data-brand-id="<?= $brand['id'] ?>">
                                        <i class="fas fa-car me-1"></i> <?= htmlspecialchars($brand['brand_name']) ?>
                                    </button>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <div class="tab-content" id="carBrandTabContent">
                                <?php foreach ($availableBrands as $index => $brand): ?>
                                <div class="tab-pane fade <?= ($index === 0 && !$selectedBrandId) || $selectedBrandId == $brand['id'] ? 'show active' : '' ?>" 
                                     id="brand-content-<?= $brand['id'] ?>" 
                                     role="tabpanel" 
                                     aria-labelledby="brand-tab-<?= $brand['id'] ?>">
                                    
                                    <?php if (!empty($carModels[$brand['id']])): ?>
                                    <div class="row row-cols-1 row-cols-md-2 g-4">
                                        <?php foreach ($carModels[$brand['id']] as $model): ?>
                                        <?php
                                        // Определяем путь к изображению автомобиля
                                        $imgFilename = '';
                                        switch($brand['id']) {
                                            case 1: // BMW
                                                if (strpos($model['model_name'], 'X5') !== false) {
                                                    $imgFilename = 'BMW X5.jpg';
                                                } elseif (strpos($model['model_name'], '5') !== false) {
                                                    $imgFilename = 'BMW 5 series.jpg';
                                                } elseif (strpos($model['model_name'], '7') !== false) {
                                                    $imgFilename = 'BMW 7 Series.jpg';
                                                }
                                                break;
                                            case 2: // Mercedes
                                                if (strpos($model['model_name'], 'GLE') !== false) {
                                                    $imgFilename = 'Mercedes GLE.jpg';
                                                } elseif (strpos($model['model_name'], 'E-Class') !== false) {
                                                    $imgFilename = 'Mercedes E-Class.jpg';
                                                } elseif (strpos($model['model_name'], 'S-Class') !== false) {
                                                    $imgFilename = 'Mercedes S-Class.jpg';
                                                }
                                                break;
                                            case 3: // Audi
                                                if (strpos($model['model_name'], 'Q5') !== false) {
                                                    $imgFilename = 'Audi Q5.jpg';
                                                } elseif (strpos($model['model_name'], 'A6') !== false) {
                                                    $imgFilename = 'Audi A6.jpg';
                                                } elseif (strpos($model['model_name'], 'A8') !== false) {
                                                    $imgFilename = 'Audi A8.jpg';
                                                }
                                                break;
                                        }
                                        
                                        // Проверяем наличие файла
                                        $imgPath = 'landing photo/' . $imgFilename;
                                        $imageExists = !empty($imgFilename) && file_exists($imgPath);
                                        
                                        // Показываем карточку только если изображение существует
                                        if ($imageExists):
                                        ?>
                                        <div class="col">
                                            <div class="card car-model-card h-100 <?= $selectedModelId == $model['id'] ? 'selected' : '' ?>" 
                                                 data-model-id="<?= $model['id'] ?>">
                                                <div class="card-img-top car-model-img">
                                                    <img src="<?= $imgPath ?>" class="img-fluid" alt="<?= htmlspecialchars($model['model_name']) ?>">
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="card-title"><i class="fas fa-car me-2"></i><?= htmlspecialchars($model['model_name']) ?></h5>
                                                    <div class="form-check">
                                                        <input class="form-check-input car-model-radio" type="radio" 
                                                               name="car_model" 
                                                               id="model_<?= $model['id'] ?>" 
                                                               value="<?= $model['id'] ?>"
                                                               <?= $selectedModelId == $model['id'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="model_<?= $model['id'] ?>">
                                                            <i class="fas fa-check-circle me-1"></i>Выбрать этот автомобиль
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Нет доступных моделей для этой марки</div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Скрытое поле для хранения выбранной марки автомобиля -->
                        <input type="hidden" id="car_brand" name="car_brand" value="<?= $selectedBrandId ?? $availableBrands[0]['id'] ?>">
                        
                        <?php if (isset($errors['car_model'])): ?>
                            <div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i><?= $errors['car_model'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="desired_date" class="form-label">Желаемая дата</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    <input type="date" class="form-control <?= isset($errors['desired_date']) ? 'is-invalid' : '' ?>" 
                                           id="desired_date" name="desired_date" value="<?= $_POST['desired_date'] ?? '' ?>">
                                    <?php if (isset($errors['desired_date'])): ?>
                                        <div class="invalid-feedback"><?= $errors['desired_date'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="desired_time" class="form-label">Желаемое время</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="time" class="form-control <?= isset($errors['desired_time']) ? 'is-invalid' : '' ?>" 
                                           id="desired_time" name="desired_time" value="<?= $_POST['desired_time'] ?? '' ?>">
                                    <?php if (isset($errors['desired_time'])): ?>
                                        <div class="invalid-feedback"><?= $errors['desired_time'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 payment-type-group">
                        <label class="form-label">Тип оплаты</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_type" id="payment_cash" value="cash" 
                                   <?= (isset($_POST['payment_type']) && $_POST['payment_type'] == 'cash') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="payment_cash">
                                <i class="fas fa-money-bill me-1"></i>Наличными
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_type" id="payment_card" value="card" 
                                   <?= (isset($_POST['payment_type']) && $_POST['payment_type'] == 'card') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="payment_card">
                                <i class="fas fa-credit-card me-1"></i>Банковская карта
                            </label>
                        </div>
                        <?php if (isset($errors['payment_type'])): ?>
                            <div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i><?= $errors['payment_type'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="agreementCheckbox" name="agreement" 
                                   <?= (isset($_POST['agreement'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="agreementCheckbox">
                                <i class="fas fa-file-contract me-1"></i>Я ознакомлен с правилами предоставления услуги тест-драйва и все предоставленные мной данные верны
                            </label>
                        </div>
                        <?php if (isset($errors['agreement'])): ?>
                            <div class="invalid-feedback d-block"><i class="fas fa-exclamation-circle me-1"></i><?= $errors['agreement'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="submitButton"><i class="fas fa-paper-plane me-2"></i>Отправить заявку</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработка клика по карточке модели
    document.querySelectorAll('.car-model-card').forEach(function(card) {
        card.addEventListener('click', function() {
            const modelId = this.dataset.modelId;
            const radio = document.getElementById('model_' + modelId);
            
            // Отмечаем радиокнопку
            radio.checked = true;
            
            // Убираем класс выбранной карточки у всех
            document.querySelectorAll('.car-model-card').forEach(function(c) {
                c.classList.remove('selected');
            });
            
            // Добавляем класс выбранной карточки
            this.classList.add('selected');
        });
    });
    
    // Обработка переключения вкладок марок
    document.querySelectorAll('#carBrandTabs .nav-link').forEach(function(tab) {
        tab.addEventListener('click', function() {
            const brandId = this.dataset.brandId;
            document.getElementById('car_brand').value = brandId;
        });
    });
});
</script> 