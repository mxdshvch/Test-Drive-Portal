// Функции для работы с модальными окнами в админ-панели
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded for admin.js');
    
    // Кэшируем DOM элементы для лучшей производительности
    const statusModal = document.getElementById('statusModal');
    const rejectReasonBlock = document.getElementById('rejection-reason-block');
    const statusSelect = document.getElementById('status');
    const rejectReason = document.getElementById('reject_reason');
    const commentField = document.getElementById('comment');
    const saveStatusBtn = document.getElementById('saveStatus');
    
    console.log('Elements found in admin.js:', {
        statusModal: !!statusModal,
        rejectReasonBlock: !!rejectReasonBlock,
        statusSelect: !!statusSelect,
        rejectReason: !!rejectReason,
        commentField: !!commentField,
        saveStatusBtn: !!saveStatusBtn
    });
    
    // Проверка наличия jQuery
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    console.log('Bootstrap modal plugin:', typeof jQuery.fn.modal !== 'undefined');
    
    // Инициализация модального окна
    if (statusModal) {
        // Обработка открытия модального окна
        $(statusModal).on('show.bs.modal', function(e) {
            console.log('Modal is showing in admin.js');
            // Устанавливаем начальное состояние формы
            const button = e.relatedTarget;
            if (!button) {
                console.error('Button not found in event');
                return;
            }
            
            const applicationId = button.getAttribute('data-id');
            const currentStatus = button.getAttribute('data-status');
            
            console.log('Application ID:', applicationId);
            console.log('Current status:', currentStatus);
            
            document.getElementById('application_id').value = applicationId;
            
            if (statusSelect) {
                statusSelect.value = currentStatus;
                handleStatusChange(currentStatus); // Инициализируем состояние формы
            }
            
            // Очищаем поле комментария
            if (commentField) {
                commentField.value = '';
            }
            
            // Сбрасываем состояние кнопки
            if (saveStatusBtn) {
                saveStatusBtn.disabled = false;
            }
        });
        
        // Добавляем обработчик для события показа модального окна
        $(statusModal).on('shown.bs.modal', function() {
            console.log('Modal is fully shown and visible');
        });
    }
    
    // Обработка изменения статуса
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            console.log('Status changed to:', this.value);
            handleStatusChange(this.value);
        });
    }
    
    function handleStatusChange(status) {
        console.log('Handling status change:', status);
        console.log('Rejection block exists:', !!rejectReasonBlock);
        
        if (rejectReasonBlock && rejectReason) {
            if (status === 'rejected') {
                console.log('Showing rejection reason block');
                rejectReasonBlock.style.display = 'block';
                rejectReason.setAttribute('required', 'required');
            } else {
                console.log('Hiding rejection reason block');
                rejectReasonBlock.style.display = 'none';
                rejectReason.removeAttribute('required');
                // Очищаем поле при смене статуса
                rejectReason.value = '';
            }
        } else {
            console.error('Rejection reason block or textarea not found');
        }
    }
    
    // Обработка отправки формы
    if (saveStatusBtn) {
        console.log('Adding click event listener to save button');
        
        // Удаляем все существующие обработчики событий с помощью клонирования элемента
        const newSaveBtn = saveStatusBtn.cloneNode(true);
        saveStatusBtn.parentNode.replaceChild(newSaveBtn, saveStatusBtn);
        
        // Обновляем ссылку на кнопку
        const updatedSaveStatusBtn = document.getElementById('saveStatus');
        console.log('Button cloned and replaced:', !!updatedSaveStatusBtn);
        
        // Добавляем новый обработчик события
        updatedSaveStatusBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Save button clicked in admin.js');
            
            const form = document.getElementById('statusForm');
            if (!form) {
                console.error('Form not found');
                return;
            }
            
            if (!form.checkValidity()) {
                console.log('Form is invalid');
                form.reportValidity();
                return;
            }
            
            // Проверяем, что причина отклонения указана, если статус "Отклонена"
            const statusSelect = document.getElementById('status');
            const rejectReason = document.getElementById('reject_reason');
            const status = statusSelect.value;
            console.log('Selected status:', status);
            
            if (status === 'rejected') {
                if (!rejectReason.value || rejectReason.value.trim() === '') {
                    console.log('Rejection reason is empty');
                    alert('Укажите причину отклонения заявки');
                    rejectReason.focus();
                    return;
                }
                console.log('Rejection reason:', rejectReason.value);
            }
            
            // Блокируем кнопку
            this.disabled = true;
            console.log('Button disabled');
            
            // Собираем данные формы
            const formData = new FormData(form);
            
            console.log('Sending data:', {
                application_id: formData.get('application_id'),
                status: formData.get('status'),
                reject_reason: formData.get('reject_reason'),
                comment: formData.get('comment')
            });
            
            // Отправляем AJAX запрос
            console.log('Sending AJAX request to:', '/demo2/actions/update_status.php');
            
            // Используем jQuery AJAX вместо fetch для лучшей совместимости
            $.ajax({
                url: '/demo2/actions/update_status.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(data) {
                    console.log('AJAX success response:', data);
                    
                    if (data.success) {
                        // Обновляем статус в таблице
                        const applicationId = formData.get('application_id');
                        const newStatus = formData.get('status');
                        const rejectionReason = formData.get('reject_reason');
                        const comment = formData.get('comment');
                        
                        console.log('Updating table with:', {
                            applicationId,
                            newStatus,
                            rejectionReason,
                            comment
                        });
                        
                        updateStatusInTable(applicationId, newStatus, rejectionReason, comment);
                        
                        // Закрываем модальное окно
                        console.log('Closing modal');
                        if (statusModal) {
                            $(statusModal).modal('hide');
                            console.log('Modal hidden');
                        }
                        
                        // Перезагружаем страницу
                        console.log('Reloading page');
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);
                    } else {
                        // Показываем сообщение об ошибке
                        console.error('Error:', data.message);
                        alert(data.message || 'Произошла ошибка при обновлении статуса');
                        
                        // Восстанавливаем кнопку
                        document.getElementById('saveStatus').disabled = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    console.log('Response text:', xhr.responseText);
                    
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        console.log('Parsed error data:', errorData);
                        alert('Ошибка: ' + (errorData.message || error));
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Произошла ошибка при отправке запроса: ' + error);
                    }
                    
                    // Восстанавливаем кнопку
                    document.getElementById('saveStatus').disabled = false;
                }
            });
        });
    }
    
    // Функция для обновления статуса в таблице
    function updateStatusInTable(applicationId, status, rejectReason, comment) {
        console.log('Updating status in table for application:', applicationId);
        
        const statusCell = document.querySelector(`tr[data-id="${applicationId}"] .status-cell`);
        const statusButton = document.querySelector(`tr[data-id="${applicationId}"] .change-status-btn`);
        
        if (statusCell) {
            // Определяем классы и текст для статуса
            let statusClass = '';
            let statusText = '';
            
            switch(status) {
                case 'new':
                    statusClass = 'badge-primary';
                    statusText = 'Новая';
                    break;
                case 'processing':
                    statusClass = 'badge-warning';
                    statusText = 'В обработке';
                    break;
                case 'approved':
                    statusClass = 'badge-success';
                    statusText = 'Одобрена';
                    break;
                case 'rejected':
                    statusClass = 'badge-danger';
                    statusText = 'Отклонена';
                    break;
                case 'completed':
                    statusClass = 'badge-info';
                    statusText = 'Завершена';
                    break;
            }
            
            console.log('New status:', statusText);
            
            // Обновляем ячейку статуса
            statusCell.innerHTML = `<span class="badge ${statusClass}">${statusText}</span>`;
            
            // Добавляем причину отклонения или комментарий
            if (status === 'rejected' && rejectReason) {
                statusCell.innerHTML += `<br><small class="text-muted">Причина: ${rejectReason}</small>`;
                console.log('Added rejection reason to table cell');
            } else if (comment && comment.trim() !== '') {
                statusCell.innerHTML += `<br><small class="text-muted">Комментарий: ${comment}</small>`;
                console.log('Added comment to table cell');
            }
            
            // Обновляем атрибут кнопки
            if (statusButton) {
                statusButton.setAttribute('data-status', status);
                console.log('Updated button data-status attribute');
            }
        } else {
            console.error('Status cell not found for application:', applicationId);
        }
    }
    
    // Проверка наличия кнопок изменения статуса
    const changeStatusButtons = document.querySelectorAll('.change-status-btn');
    console.log('Change status buttons found:', changeStatusButtons.length);
    changeStatusButtons.forEach((btn, idx) => {
        console.log(`Button ${idx}:`, {
            id: btn.getAttribute('data-id'),
            status: btn.getAttribute('data-status')
        });
    });
}); 