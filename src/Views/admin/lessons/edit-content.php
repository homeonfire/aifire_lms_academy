<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title">Контент урока: "<?= htmlspecialchars($lesson['title']) ?>"</h1>
                    <a href="javascript:history.back()" class="btn btn-secondary">Назад</a>
                </div>

                <div class="admin-card">
                    <form action="/admin/lessons/save-content/<?= $lesson['id'] ?>" method="POST" class="admin-form">
                        <input type="hidden" name="course_id" value="<?= $_GET['course_id'] ?? '' ?>">

                        <div class="input-group">
                            <label for="content_url">Ссылка на видео (YouTube, Vimeo и др.)</label>
                            <input type="url" id="content_url" name="content_url" value="<?= htmlspecialchars($lesson['content_url'] ?? '') ?>" placeholder="Оставьте пустым, если нет видео">
                        </div>

                        <div class="input-group">
                            <label for="content_text">Текст урока</label>
                            <textarea id="content_text" name="content_text" rows="15" placeholder="Оставьте пустым, если нет текста"><?= htmlspecialchars($lesson['content_text'] ?? '') ?></textarea>
                            <small>Здесь вы можете использовать HTML-теги для форматирования.</small>
                        </div>
                        <div class="input-group">
                            <label>Вопросы для домашнего задания</label>
                            <div id="homework-questions-container">
                                <?php
                                $questions = $homework ? json_decode($homework['questions'], true) : [];
                                if (!empty($questions)) {
                                    foreach ($questions as $item) {
                                        echo '<div><input type="text" name="homework_questions[]" class="homework-question-input" value="'.htmlspecialchars($item['q']).'"></div>';
                                    }
                                }
                                ?>
                            </div>
                            <button type="button" id="add-homework-question" class="btn btn-secondary" style="margin-top:10px;">+ Добавить вопрос</button>
                        </div>

                        <script>
                            document.getElementById('add-homework-question').addEventListener('click', function() {
                                const container = document.getElementById('homework-questions-container');
                                const inputDiv = document.createElement('div');
                                inputDiv.innerHTML = '<input type="text" name="homework_questions[]" class="homework-question-input" placeholder="Новый вопрос">';
                                container.appendChild(inputDiv);
                            });
                        </script>
                        <button type="submit" class="btn btn-primary">Сохранить контент</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>