// Убедимся, что DOM загружен и Bootstrap доступен
$(function() {
    const tabs = {
        '#favorites': '#favorites-content',
        '#collections': '#collections-content'
    };
    const defaultTabTarget = '#favorites-content'; // ID панели вкладки по умолчанию
    const tabContainer = document.getElementById('favoritesTabs'); // Контейнер вкладок <UL>

    // Функция для активации вкладки по ID её панели
    function activateTab(targetId) {
        // Проверяем, существует ли элемент панели
        if (!document.querySelector(targetId)) {
            console.warn(`Tab content panel with ID "${targetId}" not found.`);
            return; // Выходим, если панель не найдена
        }
        const triggerEl = document.querySelector(`button[data-bs-toggle="tab"][data-bs-target="${targetId}"]`);
        if (triggerEl) {
            // Убедимся, что Bootstrap Tab инициализирован
            if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            } else {
                console.error('Bootstrap Tab component is not available.');
            }
        } else {
             console.warn(`Tab trigger button for target "${targetId}" not found.`);
        }
    }

    // Активация вкладки при загрузке страницы
    const currentHash = window.location.hash;
    const targetFromHash = tabs[currentHash];

    if (targetFromHash) {
        activateTab(targetFromHash);
    } else {
        // Если хэша нет или он не соответствует вкладкам, активируем вкладку по умолчанию
        // но только если контейнер вкладок вообще существует на странице
        if (tabContainer) {
             activateTab(defaultTabTarget);
        }
    }

    // Обновление хэша при переключении вкладок
    if (tabContainer) {
        // Используем делегирование событий на контейнере
        tabContainer.addEventListener('shown.bs.tab', function (event) {
            // Проверяем, что event.target существует и имеет нужный атрибут
            if (event.target && event.target.getAttribute) {
                const targetId = event.target.getAttribute('data-bs-target'); // ID показанной панели (e.g., #favorites-content)
                // Находим ключ (хэш) по значению (ID панели) в объекте tabs
                const newHash = Object.keys(tabs).find(key => tabs[key] === targetId) || '';

                // Обновляем хэш, только если он действительно изменился
                if (window.location.hash !== newHash) {
                     window.location.hash = newHash;
                }
            }
        });
    } else {
        // Если контейнер вкладок не найден, скрипту нечего делать
        // console.log('Tab container #favoritesTabs not found. Tab state handler inactive.');
    }
}); 