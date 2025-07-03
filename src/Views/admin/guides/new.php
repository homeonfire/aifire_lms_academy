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
                    <form action="/admin/guides/create" method="POST" class="admin-form">
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
                            <label for="content_text">Текст гайда</label>
                            <textarea id="content_text" name="content_text" rows="15"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать гайд</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>