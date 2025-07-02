<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="course-page-wrapper">

                <div class="lesson-content-area">
                    <div class="lesson-header">
                        <a href="/dashboard" class="back-link">&larr; Назад к курсам</a>
                        <h1 class="lesson-title">
                            <?= htmlspecialchars($course['title']) ?>:
                            <span class="lesson-subtitle"><?= $activeLesson ? htmlspecialchars($activeLesson['title']) : 'Выберите урок' ?></span>
                        </h1>
                    </div>

                    <div class="lesson-viewer">
                        <?php if ($activeLesson): ?>
                            <?php if ($activeLesson['content_type'] === 'video'): ?>
                                <div class="video-player-mockup">
                                    <img src="https://placehold.co/800x450/141414/FFFFFF?text=Видео-плеер" alt="Видео плеер">
                                    <div class="play-button-mockup"></div>
                                </div>
                            <?php elseif ($activeLesson['content_type'] === 'text' || $activeLesson['content_type'] === 'guide'): ?>
                                <div class="text-content-mockup">
                                    <h3>Содержимое текстового урока или гайда</h3>
                                    <p>Здесь будет отображаться полный текст урока...</p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p>В этом курсе пока нет уроков. Скоро они появятся!</p>
                        <?php endif; ?>
                    </div>

                    <div class="lesson-description">
                        <h3>О курсе: <?= htmlspecialchars($course['title']) ?></h3>
                        <p><?= htmlspecialchars($course['description']) ?></p>
                    </div>
                </div>

                <div class="course-sidebar">
                    <div class="course-progress">
                        <h4>Прогресс курса</h4>
                        <div class="progress-bar"><div class="progress-bar-fill" style="width: 0%;"></div></div>
                        <span>0% пройдено</span>
                    </div>
                    <div class="course-content-list">
                        <h4>Содержание курса</h4>
                        <ul>
                            <?php foreach ($course['modules'] as $module): ?>
                                <li class="module-item"><?= htmlspecialchars($module['title']) ?></li>
                                <?php foreach ($module['lessons'] as $lesson): ?>
                                    <a href="/course/<?= $course['id'] ?>/lesson/<?= $lesson['id'] ?>" class="lesson-link">
                                        <li class="lesson-item <?= ($activeLesson && $lesson['id'] === $activeLesson['id']) ? 'active' : '' ?>">
                                            <?= htmlspecialchars($lesson['title']) ?>
                                        </li>
                                    </a>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>