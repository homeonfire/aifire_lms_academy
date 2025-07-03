<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title"><?= $title ?></h1>
                    <?php
                    $backLink = ($type === 'masterclass') ? '/admin/masterclasses' : '/admin/courses';
                    ?>
                    <a href="<?= $backLink ?>" class="btn btn-secondary">Назад к списку</a>
                </div>

                <div class="admin-card">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="/admin/courses/create" method="POST" class="admin-form">
                        <input type="hidden" name="type" value="<?= $type ?>">

                        <div class="input-group">
                            <label for="title">Название</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="input-group">
                            <label for="description">Описание</label>
                            <textarea id="description" name="description" rows="5"></textarea>
                        </div>
                        <div class="input-group">
                            <label>Уровень сложности</label>
                            <div class="tag-checkbox-group">
                                <label>
                                    <input type="radio" name="difficulty_level" value="beginner" checked>
                                    <span>Начинающий</span>
                                </label>
                                <label>
                                    <input type="radio" name="difficulty_level" value="intermediate">
                                    <span>Средний</span>
                                </label>
                                <label>
                                    <input type="radio" name="difficulty_level" value="advanced">
                                    <span>Продвинутый</span>
                                </label>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Выберите категорию</label>
                            <div class="tag-checkbox-group">
                                <?php foreach ($categories as $category): ?>
                                    <label>
                                        <input type="checkbox" name="category_ids[]" value="<?= $category['id'] ?>">
                                        <span><?= htmlspecialchars($category['name']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Создать</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>