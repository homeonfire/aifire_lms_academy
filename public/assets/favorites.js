document.addEventListener('DOMContentLoaded', () => {
    // Используем делегирование событий на случай, если карточки подгружаются динамически
    document.body.addEventListener('click', function(event) {
        // Находим кнопку, даже если кликнули по иконке внутри нее
        const favoriteButton = event.target.closest('.favorite-toggle-btn');

        if (favoriteButton) {
            event.preventDefault(); // Предотвращаем любые стандартные действия (например, переход по ссылке)

            const itemId = favoriteButton.dataset.itemId;
            const itemType = favoriteButton.dataset.itemType;

            if (!itemId || !itemType) {
                console.error('Favorite button is missing data-item-id or data-item-type');
                return;
            }

            // Создаем данные для отправки
            const formData = new FormData();
            formData.append('item_id', itemId);
            formData.append('item_type', itemType);

            // Отправляем AJAX запрос
            fetch('/favorite/toggle', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Важный заголовок для нашего PHP контроллера
                }
            })
                .then(response => {
                    if (!response.ok) {
                        // Если ответ не 2xx, выводим ошибку
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Если все успешно, переключаем класс 'active'
                        favoriteButton.classList.toggle('active');
                    } else {
                        // Если сервер вернул ошибку
                        console.error('Error toggling favorite:', data.message);
                        if (data.message === 'Требуется авторизация') {
                            // Можно перенаправить на страницу логина
                            window.location.href = '/login';
                        }
                    }
                })
                .catch(error => {
                    console.error('There has been a problem with your fetch operation:', error);
                });
        }
    });
});