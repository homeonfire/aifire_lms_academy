<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" class="logo">NN Academy</a>
    </div>
    <nav class="nav-menu">
        <a href="/dashboard" class="nav-item">Главная</a>
        <a href="/courses" class="nav-item">Курсы</a>
        <a href="/my-answers" class="nav-item">Мои ответы</a>
        <a href="/favorites" class="nav-item">Мои закладки</a>
        <a href="#" class="nav-item">Гайды</a>
        <a href="#" class="nav-item">Мастер-классы</a>
    </nav>
    <div class="sidebar-footer">
        <a href="#" class="nav-item theme-switcher-btn" id="theme-switcher-btn">
            <span>Сменить тему</span>
            <span class="theme-indicator"></span>
        </a>
        <?php
        // БЛОК ДЛЯ ПРОВЕРКИ ДЗ (только для админа)
        if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'):
            require_once __DIR__ . '/../../Models/HomeworkAnswer.php';
            $homeworkAnswerModel = new HomeworkAnswer();
            $submittedHomeworksCount = $homeworkAnswerModel->getSubmittedCount();
            ?>
            <a href="/homework-check" class="nav-item">
                <span>Проверка ДЗ</span>
                <?php if ($submittedHomeworksCount > 0): ?>
                    <span class="badge"><?= $submittedHomeworksCount ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <a href="/admin/dashboard" class="nav-item">
                <span>Админ-панель</span>
            </a>
        <?php endif; ?>

        <a href="/profile" class="nav-item sidebar-profile-link">
            <img src="<?= htmlspecialchars($_SESSION['user']['avatar_path'] ?? '/public/assets/images/default-avatar.png') ?>" alt="Avatar">
            <span>Профиль</span>
        </a>
        <a href="/logout" class="nav-item">Выход</a>
    </div>
</aside>