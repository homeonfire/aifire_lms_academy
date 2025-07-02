<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <h1 class="page-title">Профиль</h1>

                <form action="/profile/update" method="POST" class="profile-form">
                    <div class="form-section">
                        <h3 class="section-title">Детали профиля</h3>
                        <p class="section-subtitle">Здесь вы можете обновить информацию о себе.</p>

                        <div class="input-group">
                            <label for="first_name">Имя</label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                        </div>
                        <div class="input-group">
                            <label for="last_name">Фамилия</label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                        </div>
                        <div class="input-group">
                            <label for="email">Email адрес</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <small>Email нельзя изменить.</small>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-title">Ваш фокус</h3>
                        <p class="section-subtitle">Эта информация поможет нам рекомендовать вам подходящие курсы.</p>

                        <div class="input-group">
                            <label for="experience_level">Ваш уровень</label>
                            <select id="experience_level" name="experience_level">
                                <option value="beginner" <?= ($user['experience_level'] ?? '') === 'beginner' ? 'selected' : '' ?>>Начинающий</option>
                                <option value="intermediate" <?= ($user['experience_level'] ?? '') === 'intermediate' ? 'selected' : '' ?>>Средний</option>
                                <option value="advanced" <?= ($user['experience_level'] ?? '') === 'advanced' ? 'selected' : '' ?>>Продвинутый</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="preferred_skill_type">Интересующие навыки</label>
                            <input type="text" id="preferred_skill_type" name="preferred_skill_type" value="<?= htmlspecialchars($user['preferred_skill_type'] ?? '') ?>" placeholder="Например: Кодинг, Дизайн">
                        </div>
                    </div>

                    <button type="submit" class="btn-primary">Сохранить изменения</button>
                </form>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>