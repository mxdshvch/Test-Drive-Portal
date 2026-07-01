// Функции для анимации элементов
document.addEventListener('DOMContentLoaded', function() {
    // Добавляем классы анимации к карточкам
    document.querySelectorAll('.card').forEach(function(card, index) {
        setTimeout(function() {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Анимация для форм
    document.querySelectorAll('form').forEach(function(form) {
        form.classList.add('slide-up');
    });

    // Инициализация валидации форм
    initFormValidation();

    // Анимация появления элементов при скролле
    const animatedElements = document.querySelectorAll('.feature-card, .step-card, .model-card, .testimonial-card');
    
    // Функция проверки, есть ли элемент в видимой области
    const isElementInViewport = function(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 && 
            rect.bottom >= 0
        );
    };
    
    // Функция для анимации элементов
    const animateElements = function() {
        animatedElements.forEach(function(element) {
            if (isElementInViewport(element) && !element.classList.contains('animated')) {
                element.classList.add('animated', 'fade-in');
            }
        });
    };
    
    // Запускаем анимацию при загрузке
    animateElements();
    
    // И при скролле
    window.addEventListener('scroll', animateElements);
    
    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Затемнение навбара при скролле
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        });
    }
    
    // Анимация для кнопок
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});

// Валидация форм
function initFormValidation() {
    // Валидация формы регистрации
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            if (!validateRegisterForm()) {
                event.preventDefault();
            }
        });
    }

    // Валидация формы авторизации
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            if (!validateLoginForm()) {
                event.preventDefault();
            }
        });
    }

    // Валидация формы создания заявки
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(event) {
            if (!validateApplicationForm()) {
                event.preventDefault();
            }
        });

        // Активация кнопки отправки при отметке чекбокса
        const agreementCheckbox = document.getElementById('agreementCheckbox');
        const submitButton = document.querySelector('#applicationForm button[type="submit"]');
        
        if (agreementCheckbox && submitButton) {
            agreementCheckbox.addEventListener('change', function() {
                submitButton.disabled = !this.checked;
            });
            
            // Начальное состояние кнопки
            submitButton.disabled = !agreementCheckbox.checked;
        }
    }
}

// Валидация формы регистрации
function validateRegisterForm() {
    let isValid = true;
    
    // Валидация логина (кириллица, не менее 6 символов)
    const login = document.getElementById('login');
    if (login) {
        const loginValue = login.value.trim();
        const loginRegex = /^[а-яА-ЯёЁ]{6,}$/;
        
        if (!loginRegex.test(loginValue)) {
            showError(login, 'Логин должен содержать только кириллицу и быть не менее 6 символов');
            isValid = false;
        } else {
            clearError(login);
        }
    }
    
    // Валидация пароля (минимум 6 символов)
    const password = document.getElementById('password');
    if (password) {
        const passwordValue = password.value.trim();
        
        if (passwordValue.length < 6) {
            showError(password, 'Пароль должен содержать не менее 6 символов');
            isValid = false;
        } else {
            clearError(password);
        }
    }
    
    // Валидация ФИО (символы кириллицы и пробелы)
    const fullname = document.getElementById('fullname');
    if (fullname) {
        const fullnameValue = fullname.value.trim();
        const fullnameRegex = /^[а-яА-ЯёЁ\s]+$/;
        
        if (!fullnameRegex.test(fullnameValue)) {
            showError(fullname, 'ФИО должно содержать только символы кириллицы и пробелы');
            isValid = false;
        } else {
            clearError(fullname);
        }
    }
    
    // Валидация телефона (формат +7(XXX)-XXX-XX-XX)
    const phone = document.getElementById('phone');
    if (phone) {
        const phoneValue = phone.value.trim();
        const phoneRegex = /^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/;
        
        if (!phoneRegex.test(phoneValue)) {
            showError(phone, 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX');
            isValid = false;
        } else {
            clearError(phone);
        }
    }
    
    // Валидация email
    const email = document.getElementById('email');
    if (email) {
        const emailValue = email.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(emailValue)) {
            showError(email, 'Введите корректный адрес электронной почты');
            isValid = false;
        } else {
            clearError(email);
        }
    }
    
    return isValid;
}

// Валидация формы авторизации
function validateLoginForm() {
    let isValid = true;
    
    // Проверка заполнения логина
    const login = document.getElementById('login');
    if (login && login.value.trim() === '') {
        showError(login, 'Введите логин');
        isValid = false;
    } else {
        clearError(login);
    }
    
    // Проверка заполнения пароля
    const password = document.getElementById('password');
    if (password && password.value.trim() === '') {
        showError(password, 'Введите пароль');
        isValid = false;
    } else {
        clearError(password);
    }
    
    return isValid;
}

// Валидация формы создания заявки
function validateApplicationForm() {
    let isValid = true;
    
    // Валидация адреса
    const address = document.getElementById('address');
    if (address && address.value.trim() === '') {
        showError(address, 'Введите адрес');
        isValid = false;
    } else {
        clearError(address);
    }
    
    // Валидация телефона
    const phone = document.getElementById('contact_phone');
    if (phone) {
        const phoneValue = phone.value.trim();
        const phoneRegex = /^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/;
        
        if (!phoneRegex.test(phoneValue)) {
            showError(phone, 'Телефон должен быть в формате +7(XXX)-XXX-XX-XX');
            isValid = false;
        } else {
            clearError(phone);
        }
    }
    
    // Валидация водительского удостоверения
    const driverLicense = document.getElementById('driver_license');
    if (driverLicense && driverLicense.value.trim() === '') {
        showError(driverLicense, 'Введите номер водительского удостоверения');
        isValid = false;
    } else {
        clearError(driverLicense);
    }
    
    // Валидация даты выдачи водительского удостоверения
    const licenseDate = document.getElementById('license_date');
    if (licenseDate && licenseDate.value.trim() === '') {
        showError(licenseDate, 'Введите дату выдачи водительского удостоверения');
        isValid = false;
    } else {
        clearError(licenseDate);
    }
    
    // Валидация марки автомобиля
    const carBrand = document.getElementById('car_brand');
    if (carBrand && carBrand.value === '') {
        showError(carBrand, 'Выберите марку автомобиля');
        isValid = false;
    } else {
        clearError(carBrand);
    }
    
    // Валидация модели автомобиля
    const carModel = document.getElementById('car_model');
    if (carModel && carModel.value === '') {
        showError(carModel, 'Выберите модель автомобиля');
        isValid = false;
    } else {
        clearError(carModel);
    }
    
    // Валидация даты и времени
    const desiredDate = document.getElementById('desired_date');
    if (desiredDate && desiredDate.value.trim() === '') {
        showError(desiredDate, 'Выберите желаемую дату');
        isValid = false;
    } else {
        clearError(desiredDate);
    }
    
    const desiredTime = document.getElementById('desired_time');
    if (desiredTime && desiredTime.value.trim() === '') {
        showError(desiredTime, 'Выберите желаемое время');
        isValid = false;
    } else {
        clearError(desiredTime);
    }
    
    // Валидация типа оплаты
    const paymentType = document.querySelector('input[name="payment_type"]:checked');
    if (!paymentType) {
        showError(document.querySelector('.payment-type-group'), 'Выберите тип оплаты');
        isValid = false;
    } else {
        clearError(document.querySelector('.payment-type-group'));
    }
    
    return isValid;
}

// Функция для отображения ошибки
function showError(element, message) {
    const formGroup = element.closest('.form-group') || element.closest('.mb-3');
    
    // Удаляем предыдущую ошибку, если она есть
    const existingError = formGroup.querySelector('.invalid-feedback');
    if (existingError) {
        existingError.remove();
    }
    
    // Добавляем класс is-invalid
    element.classList.add('is-invalid');
    
    // Создаем элемент с сообщением об ошибке
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    // Добавляем сообщение после элемента
    element.after(errorDiv);
}

// Функция для очистки ошибки
function clearError(element) {
    element.classList.remove('is-invalid');
    
    const formGroup = element.closest('.form-group') || element.closest('.mb-3');
    const errorDiv = formGroup.querySelector('.invalid-feedback');
    
    if (errorDiv) {
        errorDiv.remove();
    }
}

// Динамическая загрузка моделей при выборе марки автомобиля
document.addEventListener('DOMContentLoaded', function() {
    const carBrandSelect = document.getElementById('car_brand');
    const carModelSelect = document.getElementById('car_model');
    
    if (carBrandSelect && carModelSelect) {
        carBrandSelect.addEventListener('change', function() {
            const brandId = this.value;
            
            // Очищаем текущий список моделей
            carModelSelect.innerHTML = '<option value="">Выберите модель</option>';
            
            if (brandId) {
                // Отправляем AJAX запрос для получения моделей
                fetch('get_models.php?brand_id=' + brandId)
                    .then(response => response.json())
                    .then(data => {
                        // Добавляем полученные модели в выпадающий список
                        data.forEach(model => {
                            const option = document.createElement('option');
                            option.value = model.id;
                            option.textContent = model.model_name;
                            carModelSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Ошибка загрузки моделей:', error));
            }
        });
    }
}); 