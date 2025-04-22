/**
 * JavaScript для страницы избранного
 * 
 * Функционал:
 * - Удаление элементов из избранного с анимацией
 * - AJAX-запросы на сервер
 * - Отображение всплывающих уведомлений
 */

document.addEventListener('DOMContentLoaded', function() {
    // Инициализация всплывающих уведомлений
    const toastElList = [].slice.call(document.querySelectorAll('.toast'));
    const toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000
        });
    });

    // Обработка удаления из избранного
    document.querySelectorAll('.favorite-toggle-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.getAttribute('data-property-id');
            const favoriteItem = this.closest('.favorite-item');
            
            // Добавляем класс для анимации
            favoriteItem.classList.add('removing');
            
            // Имитация AJAX-запроса к серверу
            setTimeout(() => {
                // Имитируем запрос к серверу
                mockRemoveFromFavorites(propertyId)
                    .then(response => {
                        if (response.success) {
                            // Удаляем элемент из DOM после завершения анимации
                            setTimeout(() => {
                                favoriteItem.remove();
                                
                                // Если больше нет элементов, показываем сообщение о пустом списке
                                if (document.querySelectorAll('.favorite-item').length === 0) {
                                    showEmptyFavoritesList();
                                }
                            }, 300);
                            
                            // Показываем уведомление
                            const toast = document.getElementById('favoriteToast');
                            const bsToast = bootstrap.Toast.getInstance(toast) || new bootstrap.Toast(toast);
                            document.querySelector('#favoriteToast .toast-body').textContent = 'Объявление удалено из избранного';
                            bsToast.show();
                        } else {
                            // В случае ошибки отменяем анимацию и показываем сообщение
                            favoriteItem.classList.remove('removing');
                            document.querySelector('#favoriteToast .toast-body').textContent = 'Ошибка при удалении из избранного';
                            bsToast.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error removing from favorites:', error);
                        favoriteItem.classList.remove('removing');
                        
                        // Показываем уведомление об ошибке
                        const toast = document.getElementById('favoriteToast');
                        const bsToast = bootstrap.Toast.getInstance(toast) || new bootstrap.Toast(toast);
                        document.querySelector('#favoriteToast .toast-body').textContent = 'Произошла ошибка при удалении';
                        bsToast.show();
                    });
            }, 300);
        });
    });

    /**
     * Мок функция для имитации AJAX-запроса на удаление из избранного
     * 
     * @param {number} propertyId ID объявления
     * @returns {Promise} Promise с результатом операции
     */
    function mockRemoveFromFavorites(propertyId) {
        return new Promise((resolve, reject) => {
            // Имитация задержки сети
            setTimeout(() => {
                // Имитация успешного ответа (можно добавить случайную ошибку для тестирования)
                const success = Math.random() > 0.1; // 10% вероятность ошибки
                
                if (success) {
                    resolve({
                        success: true,
                        message: 'Объявление успешно удалено из избранного'
                    });
                } else {
                    resolve({
                        success: false,
                        message: 'Ошибка при удалении из избранного'
                    });
                }
            }, 700); // Имитация задержки сети
        });
    }

    /**
     * Показывает сообщение о пустом списке избранного
     */
    function showEmptyFavoritesList() {
        const grid = document.getElementById('favorites-grid');
        const container = document.querySelector('.favorites-container');
        
        // Создаем элемент с сообщением о пустом списке
        const emptyMessage = document.createElement('div');
        emptyMessage.className = 'empty-favorites-message text-center py-5';
        emptyMessage.innerHTML = `
            <i class="fas fa-heart text-muted" style="font-size: 48px;"></i>
            <h3 class="mt-3">У вас пока нет избранных объявлений</h3>
            <p class="text-muted mb-4">Добавляйте понравившиеся объявления в избранное, чтобы они отображались здесь</p>
            <a href="/" class="btn btn-primary">Перейти к поиску объявлений</a>
        `;
        
        // Удаляем сетку и добавляем сообщение
        grid.remove();
        container.appendChild(emptyMessage);
    }
});
