/**
 * Компонент кнопки "Избранное"
 * 
 * Функционал:
 * - Добавление/удаление объявлений из избранного
 * - Обработка AJAX-запросов
 * - Уведомления о результате операции
 */

class FavoriteButton {
    /**
     * Инициализирует компонент кнопки избранного
     * 
     * @param {string} selector CSS селектор для кнопок избранного
     * @param {Object} options Настройки компонента
     */
    constructor(selector = '.favorite-btn', options = {}) {
        this.selector = selector;
        this.options = Object.assign({
            // Значения по умолчанию
            activeClass: 'active',
            addToFavoritesUrl: '/favorites/add',
            removeFromFavoritesUrl: '/favorites/remove',
            showNotifications: true,
            // Коллбеки
            onSuccess: null,
            onError: null,
            onAdd: null,
            onRemove: null
        }, options);
        
        this.init();
    }
    
    /**
     * Инициализирует обработчики событий
     */
    init() {
        // Находим все кнопки и добавляем обработчики
        document.querySelectorAll(this.selector).forEach(button => {
            button.addEventListener('click', this.handleClick.bind(this));
        });
        
        // Инициализируем уведомления, если необходимо
        if (this.options.showNotifications) {
            this.initNotifications();
        }
    }
    
    /**
     * Инициализирует компонент уведомлений, если его еще нет на странице
     */
    initNotifications() {
        if (!document.getElementById('favoriteToast')) {
            // Добавляем стили
            const style = document.createElement('style');
            style.textContent = `
                .toast-container {
                    position: fixed;
                    bottom: 0;
                    right: 0;
                    padding: 1rem;
                    z-index: 1000;
                }
                .toast {
                    background: white;
                    border-radius: 4px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    padding: 1rem;
                    margin-bottom: 0.5rem;
                    min-width: 250px;
                    opacity: 0;
                    transition: opacity 0.3s ease;
                    display: none;
                }
                .toast.bg-danger {
                    background: #dc3545;
                    color: white;
                }
                .toast-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 0.5rem;
                }
                .toast-body {
                    margin: 0;
                }
                .btn-close {
                    background: none;
                    border: none;
                    font-size: 1.25rem;
                    cursor: pointer;
                    padding: 0;
                    color: inherit;
                }
            `;
            document.head.appendChild(style);

            // Создаем контейнер для уведомлений
            const toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            
            // Создаем уведомление
            const toast = document.createElement('div');
            toast.id = 'favoriteToast';
            toast.className = 'toast';
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.innerHTML = `
                <div class="toast-header">
                    <strong class="me-auto">Уведомление</strong>
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.style.display='none'">&times;</button>
                </div>
                <div class="toast-body">
                    Объявление добавлено в избранное
                </div>
            `;
            
            // Добавляем в DOM
            toastContainer.appendChild(toast);
            document.body.appendChild(toastContainer);
        }
    }
    
    /**
     * Обработчик клика по кнопке
     * 
     * @param {Event} event Событие клика
     */
    handleClick(event) {
        event.preventDefault();
        
        const button = event.currentTarget;
        const propertyId = button.getAttribute('data-property-id');
        const isActive = button.classList.contains(this.options.activeClass);
        
        if (isActive) {
            this.removeFromFavorites(propertyId, button);
        } else {
            this.addToFavorites(propertyId, button);
        }
    }
    
    /**
     * Добавляет объявление в избранное
     * 
     * @param {number} propertyId ID объявления
     * @param {HTMLElement} button Кнопка, которая была нажата
     */
    addToFavorites(propertyId, button) {
        // Добавляем класс активности до получения ответа сервера для мгновенной обратной связи
        button.classList.add(this.options.activeClass);
        
        // В реальном проекте здесь будет AJAX-запрос к серверу
        this.mockAjaxRequest(this.options.addToFavoritesUrl, { propertyId })
            .then(response => {
                if (response.success) {
                    // Вызываем коллбек при успешном добавлении
                    if (typeof this.options.onAdd === 'function') {
                        this.options.onAdd(propertyId, button);
                    }
                    
                    // Показываем уведомление
                    if (this.options.showNotifications) {
                        this.showNotification('Объявление добавлено в избранное');
                    }
                } else {
                    // Если произошла ошибка, возвращаем внешний вид кнопки в исходное состояние
                    button.classList.remove(this.options.activeClass);
                    
                    // Показываем уведомление об ошибке
                    if (this.options.showNotifications) {
                        this.showNotification('Ошибка при добавлении в избранное', 'error');
                    }
                }
                
                // Вызываем общий коллбек
                if (typeof this.options.onSuccess === 'function') {
                    this.options.onSuccess(response, propertyId, button);
                }
            })
            .catch(error => {
                console.error('Error adding to favorites:', error);
                
                // Возвращаем внешний вид кнопки в исходное состояние
                button.classList.remove(this.options.activeClass);
                
                // Показываем уведомление об ошибке
                if (this.options.showNotifications) {
                    this.showNotification('Произошла ошибка при добавлении в избранное', 'error');
                }
                
                // Вызываем коллбек при ошибке
                if (typeof this.options.onError === 'function') {
                    this.options.onError(error, propertyId, button);
                }
            });
    }
    
    /**
     * Удаляет объявление из избранного
     * 
     * @param {number} propertyId ID объявления
     * @param {HTMLElement} button Кнопка, которая была нажата
     */
    removeFromFavorites(propertyId, button) {
        // Снимаем класс активности до получения ответа сервера для мгновенной обратной связи
        button.classList.remove(this.options.activeClass);
        
        // В реальном проекте здесь будет AJAX-запрос к серверу
        this.mockAjaxRequest(this.options.removeFromFavoritesUrl, { propertyId })
            .then(response => {
                if (response.success) {
                    // Вызываем коллбек при успешном удалении
                    if (typeof this.options.onRemove === 'function') {
                        this.options.onRemove(propertyId, button);
                    }
                    
                    // Показываем уведомление
                    if (this.options.showNotifications) {
                        this.showNotification('Объявление удалено из избранного');
                    }
                } else {
                    // Если произошла ошибка, возвращаем внешний вид кнопки в исходное состояние
                    button.classList.add(this.options.activeClass);
                    
                    // Показываем уведомление об ошибке
                    if (this.options.showNotifications) {
                        this.showNotification('Ошибка при удалении из избранного', 'error');
                    }
                }
                
                // Вызываем общий коллбек
                if (typeof this.options.onSuccess === 'function') {
                    this.options.onSuccess(response, propertyId, button);
                }
            })
            .catch(error => {
                console.error('Error removing from favorites:', error);
                
                // Возвращаем внешний вид кнопки в исходное состояние
                button.classList.add(this.options.activeClass);
                
                // Показываем уведомление об ошибке
                if (this.options.showNotifications) {
                    this.showNotification('Произошла ошибка при удалении из избранного', 'error');
                }
                
                // Вызываем коллбек при ошибке
                if (typeof this.options.onError === 'function') {
                    this.options.onError(error, propertyId, button);
                }
            });
    }
    
    /**
     * Показывает уведомление
     * 
     * @param {string} message Текст уведомления
     * @param {string} type Тип уведомления: 'success' или 'error'
     */
    showNotification(message, type = 'success') {
        const toast = document.getElementById('favoriteToast');
        if (!toast) return;
        
        // Обновляем текст и стиль уведомления
        const toastBody = toast.querySelector('.toast-body');
        toastBody.textContent = message;
        
        // Устанавливаем цвет в зависимости от типа
        if (type === 'error') {
            toast.classList.add('bg-danger', 'text-white');
        } else {
            toast.classList.remove('bg-danger', 'text-white');
        }
        
        // Показываем уведомление
        toast.style.display = 'block';
        toast.style.opacity = '1';
        
        // Скрываем через 3 секунды
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }, 3000);
    }
    
    /**
     * Имитация AJAX-запроса (для демонстрации)
     * 
     * @param {string} url URL для запроса
     * @param {Object} data Данные для отправки
     * @returns {Promise} Promise с результатом запроса
     */
    mockAjaxRequest(url, data) {
        return new Promise((resolve, reject) => {
            // Имитация задержки сети
            setTimeout(() => {
                // Имитация случайной ошибки (10% вероятность)
                const success = Math.random() > 0.1;
                
                if (success) {
                    resolve({
                        success: true,
                        message: 'Операция выполнена успешно',
                        data: {
                            propertyId: data.propertyId,
                            url: url
                        }
                    });
                } else {
                    resolve({
                        success: false,
                        message: 'Ошибка при выполнении операции',
                        error: 'server_error'
                    });
                }
            }, 700); // Имитация задержки сети
        });
    }
}

// Автоматическая инициализация на странице
document.addEventListener('DOMContentLoaded', () => {
    // Инициализируем компонент для всех кнопок избранного на странице
    window.favoriteButtonInstance = new FavoriteButton('.favorite-btn', {
        // Пользовательские настройки можно передать здесь
        onAdd: (propertyId, button) => {
            console.log(`Property ID ${propertyId} added to favorites`);
        },
        onRemove: (propertyId, button) => {
            console.log(`Property ID ${propertyId} removed from favorites`);
            
            // На странице избранного удаляем карточку при удалении из избранного
            if (window.location.pathname === '/favorites') {
                const favoriteItem = button.closest('.favorite-item');
                if (favoriteItem) {
                    favoriteItem.classList.add('removing');
                    setTimeout(() => {
                        favoriteItem.remove();
                        
                        // Если больше нет элементов, показываем сообщение о пустом списке
                        if (document.querySelectorAll('.favorite-item').length === 0) {
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
                    }, 300);
                }
            }
        }
    });
});

// Экспортируем класс для возможности использования в других скриптах
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FavoriteButton;
}