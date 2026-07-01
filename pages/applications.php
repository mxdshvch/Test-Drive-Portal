<?php
// Проверка авторизации
if (!isLoggedIn()) {
    header("Location: index.php?page=login");
    exit();
}

// Получение ID пользователя
$userId = $_SESSION['user_id'];

// Получение заявок пользователя
try {
    $stmt = $pdo->prepare("
        SELECT a.*, b.brand_name, m.model_name, 
        CASE 
            WHEN a.status = 'new' THEN 'Новая'
            WHEN a.status = 'processing' THEN 'В обработке'
            WHEN a.status = 'approved' THEN 'Одобрено'
            WHEN a.status = 'completed' THEN 'Выполнено'
            WHEN a.status = 'rejected' THEN 'Отклонено'
            ELSE a.status
        END as status_text
        FROM applications a
        JOIN car_models m ON a.car_model_id = m.id
        JOIN car_brands b ON m.brand_id = b.id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([$userId]);
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Ошибка при получении заявок: ' . $e->getMessage();
}
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <img src="Logo.svg" alt="Логотип" class="logo me-2">
                    <div>
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#history" data-bs-toggle="tab">
                                    <i class="fas fa-history me-2"></i>История заявок
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#new" data-bs-toggle="tab">
                                    <i class="fas fa-plus-circle me-2"></i>Новая заявка
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- История заявок -->
                    <div class="tab-pane fade show active" id="history">
                        <h5 class="card-title mb-4"><i class="fas fa-clipboard-list me-2"></i>История заявок</h5>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (empty($applications)): ?>
                            <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>У вас пока нет заявок.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><i class="fas fa-calendar-alt me-1"></i> Дата создания</th>
                                            <th><i class="fas fa-car me-1"></i> Автомобиль</th>
                                            <th><i class="fas fa-clock me-1"></i> Дата и время тест-драйва</th>
                                            <th><i class="fas fa-tag me-1"></i> Статус</th>
                                            <th><i class="fas fa-comment me-1"></i> Комментарий</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $app): ?>
                                            <tr data-id="<?= $app['id'] ?>">
                                                <td><?= date('d.m.Y', strtotime($app['created_at'])) ?></td>
                                                <td><i class="fas fa-car-side me-1"></i> <?= htmlspecialchars($app['brand_name'] . ' ' . $app['model_name']) ?></td>
                                                <td><i class="fas fa-calendar-check me-1"></i> <?= date('d.m.Y', strtotime($app['desired_date'])) . ' ' . date('H:i', strtotime($app['desired_time'])) ?></td>
                                                <td class="status-cell">
                                                    <?php
                                                    $statusClass = '';
                                                    switch ($app['status']) {
                                                        case 'new': 
                                                            $statusClass = 'badge-primary'; 
                                                            break;
                                                        case 'processing': 
                                                            $statusClass = 'badge-warning'; 
                                                            break;
                                                        case 'approved': 
                                                            $statusClass = 'badge-success'; 
                                                            break;
                                                        case 'completed': 
                                                            $statusClass = 'badge-info'; 
                                                            break;
                                                        case 'rejected': 
                                                            $statusClass = 'badge-danger'; 
                                                            break;
                                                        default:
                                                            $statusClass = 'badge-secondary';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $statusClass ?>"><?= $app['status_text'] ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($app['status'] === 'rejected' && !empty($app['rejection_reason'])): ?>
                                                        <i class="fas fa-comment-dots me-1"></i> <?= htmlspecialchars($app['rejection_reason']) ?>
                                                    <?php elseif (!empty($app['comment'])): ?>
                                                        <i class="fas fa-comment me-1"></i> <?= htmlspecialchars($app['comment']) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Новая заявка -->
                    <div class="tab-pane fade" id="new">
                        <h5 class="card-title mb-4"><i class="fas fa-file-alt me-2"></i>Новая заявка</h5>
                        <a href="index.php?page=new_application" class="btn btn-primary">
                            <i class="fas fa-plus-circle me-2"></i>Создать новую заявку
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html> 