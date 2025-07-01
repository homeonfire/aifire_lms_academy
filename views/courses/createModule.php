<?php include_once "tpl/academy-header.php"; ?>
    <section class="header main-page">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h1 class="banner-heading">Создание модуля</h1>
                </div>
            </div>

        </div>
    </section>
    <section>
        <div class="container">
            <div class="form-container">
                <h1>Создание модуля</h1>
                <div class="input-group hidden">
                    <label for="module-title">ID Курса:</label>
                    <input type="text" id="course_id" placeholder="Введите название модуля" value="<?= htmlspecialchars($course_id) ?>">
                </div>
                <div class="input-group">
                    <label for="module-title">Название модуля:</label>
                    <input type="text" id="module-title" placeholder="Введите название модуля">
                </div>
                <div class="input-group">
                    <label for="module-description">Описание модуля:</label>
                    <textarea id="module-description" placeholder="Введите описание модуля"></textarea>
                </div>
                <div class="input-group">
                    <label for="module-number">Прядковый номер модуля:</label>
                    <input id="module-number" placeholder="Введите порядковый номер модуля"></input>
                </div>
                <div class="input-group">
                    <label for="module-emoji">Выберите эмодзи:</label>
                    <select id="module-emoji">
                        <option value="😀">😀</option>
                        <option value="😎">😎</option>
                        <option value="🤓">🤓</option>
                        <option value="📚">📚</option>
                        <option value="🎓">🎓</option>
                        <option value="💡">💡</option>
                        <option value="🚀">🚀</option>
                        <option value="🌟">🌟</option>
                        <option value="🔥">🔥</option>
                        <option value="💻">💻</option>
                        <!-- Добавьте другие эмодзи по вашему усмотрению -->
                    </select>
                </div>
                <button id="save-lesson">Сохранить курс</button>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('save-lesson').addEventListener('click', function() {
            const courseTitle = document.getElementById('module-title').value;
            const courseDescription = document.getElementById('module-description').value;
            const courseEmoji = document.getElementById('module-emoji').value;
            const courseId = document.getElementById('course_id').value;
            const moduleNum = document.getElementById('module-number').value;

            // Проверяем, что все поля заполнены
            if (!courseTitle || !courseDescription || !courseEmoji || !courseId || !moduleNum) {
                alert('Пожалуйста, заполните все поля.');
                return;
            }

            const courseData = {
                title: courseTitle,
                description: courseDescription,
                emojis: courseEmoji,
                courseId: courseId,
                order_number: moduleNum,
            };

            console.log(JSON.stringify(courseData));

            fetch('/courses/storeModule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(courseData)
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Ошибка сети');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.message);
                })
                .catch(error => {
                    console.error('Ошибка:', error);
                    alert('Произошла ошибка при сохранении курса.');
                });
        });
    </script>
<?php include_once "tpl/academy-footer.php"; ?>