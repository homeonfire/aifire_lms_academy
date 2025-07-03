<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>
        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title">Новый гайд</h1>
                    <a href="/admin/guides" class="btn btn-secondary">Назад к списку</a>
                </div>
                <div class="admin-card">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></div>
                    <?php endif; ?>
                    <form id="content-form" action="/admin/guides/create" method="POST" class="admin-form">
                        <div class="input-group">
                            <label for="title">Название гайда</label>
                            <input type="text" id="title" name="title" required>
                        </div>

                        <div class="input-group">
                            <label>Уровень сложности</label>
                            <div class="tag-checkbox-group">
                                <label><input type="radio" name="difficulty_level" value="beginner" checked><span>Начинающий</span></label>
                                <label><input type="radio" name="difficulty_level" value="intermediate"><span>Средний</span></label>
                                <label><input type="radio" name="difficulty_level" value="advanced"><span>Продвинутый</span></label>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Категории</label>
                            <div class="tag-checkbox-group">
                                <?php foreach ($categories as $category): ?>
                                    <label><input type="checkbox" name="category_ids[]" value="<?= $category['id'] ?>"><span><?= htmlspecialchars($category['name']) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="content_url">Ссылка на видео (необязательно)</label>
                            <input type="url" id="content_url" name="content_url">
                        </div>

                        <div class="input-group">
                            <label>Текст гайда</label>
                            <div id="editorjs" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 10px; min-height: 300px;"></div>
                            <input type="hidden" name="content_json" id="content_json_output">
                        </div>
                        <button type="submit" class="btn btn-primary">Создать гайд</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

<?php
// ДОБАВЛЕНО: JavaScript для инициализации Editor.js
// Для нового гайда, данные Editor.js будут пустыми
?>
    <script>
        window.editorData = {};
    </script>

<?php $this->render('layouts/footer'); ?>