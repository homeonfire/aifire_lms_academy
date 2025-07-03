<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>
        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title">Редактировать гайд</h1>
                    <a href="/admin/guides" class="btn btn-secondary">Назад к списку</a>
                </div>
                <div class="admin-card">
                    <form action="/admin/guides/update/<?= $guide['id'] ?>" method="POST" class="admin-form">
                        <div class="input-group">
                            <label for="title">Название гайда</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($guide['title']) ?>" required>
                        </div>

                        <div class="input-group">
                            <label>Уровень сложности</label>
                            <div class="tag-checkbox-group">
                                <label><input type="radio" name="difficulty_level" value="beginner" <?= ($guide['difficulty_level'] ?? 'beginner') === 'beginner' ? 'checked' : '' ?>><span>Начинающий</span></label>
                                <label><input type="radio" name="difficulty_level" value="intermediate" <?= ($guide['difficulty_level'] ?? '') === 'intermediate' ? 'checked' : '' ?>><span>Средний</span></label>
                                <label><input type="radio" name="difficulty_level" value="advanced" <?= ($guide['difficulty_level'] ?? '') === 'advanced' ? 'checked' : '' ?>><span>Продвинутый</span></label>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Категории</label>
                            <div class="tag-checkbox-group">
                                <?php foreach ($categories as $category): ?>
                                    <?php $isChecked = in_array($category['id'], $guideCategoryIds); ?>
                                    <label><input type="checkbox" name="category_ids[]" value="<?= $category['id'] ?>" <?= $isChecked ? 'checked' : '' ?>><span><?= htmlspecialchars($category['name']) ?></span></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="input-group">
                            <label for="content_url">Ссылка на видео (необязательно)</label>
                            <input type="url" id="content_url" name="content_url" value="<?= htmlspecialchars($guide['content_url'] ?? '') ?>">
                        </div>
                        <div class="input-group">
                            <label for="content_text">Текст гайда</label>
                            <textarea id="content_text" name="content_text" rows="15"><?= htmlspecialchars($guide['content_text'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>