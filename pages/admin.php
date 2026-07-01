<?php
// Проверка прав администратора
if (!isAdmin()) {
    header("Location: index.php?page=login");
    exit();
}

// Получение всех заявок с пагинацией
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Фильтрация по статусу
$statusFilter = isset($_GET['status']) && in_array($_GET['status'], ['new', 'processing', 'approved', 'rejected', 'completed']) ? $_GET['status'] : '';

try {
    // Подготовка условия фильтрации
    $whereClause = $statusFilter ? "WHERE a.status = ?" : "";
    $params = $statusFilter ? [$statusFilter] : [];
    
    // Запрос для получения общего количества заявок
    $countQuery = "SELECT COUNT(*) FROM applications a $whereClause";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($params);
    $totalApplications = $countStmt->fetchColumn();
    
    // Вычисление общего количества страниц
    $totalPages = ceil($totalApplications / $perPage);
    
    // Запрос для получения заявок с пагинацией
    $query = "
        SELECT a.*, u.fullname, u.phone as user_phone, u.email, b.brand_name, m.model_name
        FROM applications a
        JOIN users u ON a.user_id = u.id
        JOIN car_models m ON a.car_model_id = m.id
        JOIN car_brands b ON m.brand_id = b.id
        $whereClause
        ORDER BY a.created_at DESC
        LIMIT $perPage OFFSET $offset
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'Ошибка при получении заявок: ' . $e->getMessage();
}
?>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    
                    <h4><i class="fas fa-cogs me-2"></i>Панель администратора</h4>
                </div>
            </div>
            <div class="card-body">
                <!-- Фильтры -->
                <div class="mb-4">
                    <form method="get" action="index.php" class="row g-3">
                        <input type="hidden" name="page" value="admin">
                        <div class="col-md-4">
                            <label for="statusFilter" class="form-label">Фильтр по статусу</label>
                            <select name="status" id="statusFilter" class="form-select">
                                <option value="">Все статусы</option>
                                <option value="new" <?= $statusFilter === 'new' ? 'selected' : '' ?>>Новая</option>
                                <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>В обработке</option>
                                <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Одобрена</option>
                                <option value="rejected" <?= $statusFilter === 'rejected' ? 'selected' : '' ?>>Отклонена</option>
                                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Завершена</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>Фильтровать</button>
                        </div>
                    </form>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $error ?></div>
                <?php endif; ?>
                
                <?php if (empty($applications)): ?>
                    <div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Заявок не найдено.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>        
                                    <th><i class="fas fa-hashtag me-1"></i>ID</th>
                                    <th><i class="fas fa-calendar-alt me-1"></i>Дата создания</th>
                                    <th><i class="fas fa-user me-1"></i>Клиент</th>
                                    <th><i class="fas fa-address-card me-1"></i>Контакты</th>
                                    <th><i class="fas fa-car me-1"></i>Автомобиль</th>
                                    <th><i class="fas fa-clock me-1"></i>Дата и время</th>
                                    <th><i class="fas fa-tag me-1"></i>Статус</th>
                                    <th><i class="fas fa-cog me-1"></i>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr data-id="<?= $app['id'] ?>">
                                        <td><?= $app['id'] ?></td>                                        
                                        <td><?= date('d.m.Y H:i', strtotime($app['created_at'])) ?></td>
                                        <td><?= htmlspecialchars($app['fullname']) ?></td>
                                        <td>
                                            <div><i class="fas fa-phone me-1"></i> <?= htmlspecialchars($app['phone']) ?></div>
                                            <div><i class="fas fa-envelope me-1"></i> <?= htmlspecialchars($app['email']) ?></div>
                                        </td>
                                        <td><i class="fas fa-car-side me-1"></i> <?= htmlspecialchars($app['brand_name'] . ' ' . $app['model_name']) ?></td>
                                        <td><i class="fas fa-calendar-check me-1"></i> <?= date('d.m.Y', strtotime($app['desired_date'])) . ' ' . date('H:i', strtotime($app['desired_time'])) ?></td>
                                        <td class="status-cell">
                                            <?php
                                            $statusClass = '';
                                            switch ($app['status']) {
                                                case 'new': 
                                                    $statusClass = 'badge-primary'; 
                                                    $statusText = 'Новая';
                                                    break;
                                                case 'processing': 
                                                    $statusClass = 'badge-warning'; 
                                                    $statusText = 'В обработке';
                                                    break;
                                                case 'approved': 
                                                    $statusClass = 'badge-success'; 
                                                    $statusText = 'Одобрена';
                                                    break;
                                                case 'completed': 
                                                    $statusClass = 'badge-info'; 
                                                    $statusText = 'Завершена';
                                                    break;
                                                case 'rejected': 
                                                    $statusClass = 'badge-danger'; 
                                                    $statusText = 'Отклонена';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = $app['status'];
                                            }
                                            ?>
                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                            <?php if ($app['status'] === 'rejected' && !empty($app['rejection_reason'])): ?>
                                                <br><small class="text-muted">Причина: <?= htmlspecialchars($app['rejection_reason']) ?></small>
                                            <?php elseif (!empty($app['comment'])): ?>
                                                <br><small class="text-muted">Комментарий: <?= htmlspecialchars($app['comment']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary change-status-btn" 
                                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                                    data-id="<?= $app['id'] ?>" data-status="<?= $app['status'] ?>">
                                                <i class="fas fa-edit me-1"></i> Изменить статус
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Пагинация -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Навигация по страницам">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="index.php?page=admin&p=<?= $page - 1 ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>"><i class="fas fa-chevron-left me-1"></i> Предыдущая</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="index.php?page=admin&p=<?= $i ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                    <a class="page-link" href="index.php?page=admin&p=<?= $page + 1 ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">Следующая <i class="fas fa-chevron-right ms-1"></i></a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Единое модальное окно для изменения статуса -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Изменение статуса заявки
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="application_id" name="application_id" value="">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Статус</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="new">Новая</option>
                            <option value="processing">В обработке</option>
                            <option value="approved">Одобрена</option>
                            <option value="completed">Завершена</option>
                            <option value="rejected">Отклонена</option>
                        </select>
                    </div>
                    
                    <!-- Блок для комментария -->
                    <div class="mb-3">
                        <label for="comment" class="form-label">Комментарий</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Добавьте комментарий к заявке"></textarea>
                        <div class="form-text">Комментарий будет виден клиенту</div>
                    </div>
                    
                    <!-- Блок для причины отклонения -->
                    <div id="rejection-reason-block" style="display: none;">
                        <div class="mb-3">
                            <label for="reject_reason" class="form-label">Причина отклонения</label>
                            <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Отмена
                    </button>
                    <button type="button" class="btn btn-primary" id="saveStatus">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Добавляем скрипт для отладки -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin page loaded');
    
    // Проверяем наличие элементов формы
    const statusModal = document.getElementById('statusModal');
    const statusForm = document.getElementById('statusForm');
    const saveStatusBtn = document.getElementById('saveStatus');
    
    console.log('Elements found on admin page:', {
        statusModal: !!statusModal,
        statusForm: !!statusForm,
        saveStatusBtn: !!saveStatusBtn
    });
    
    if (statusForm && saveStatusBtn) {
        // Добавляем прямой обработчик для формы
        statusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted via submit event');
        });
    }
});
</script>