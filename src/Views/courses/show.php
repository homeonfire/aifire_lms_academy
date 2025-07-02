<?php
// --- Функция для встраивания видео ---
function getVideoEmbedUrl($url) {
    if (empty($url)) return null;
    if (preg_match('/kinescope\.io\/([a-zA-Z0-9]+)/', $url, $matches)) return 'https://kinescope.io/embed/' . $matches[1];
    if (preg_match('/(v=|\/v\/|youtu\.be\/|embed\/)([^"&?\/ ]{11})/', $url, $matches)) return 'https://www.youtube.com/embed/' . $matches[2];
    return null;
}
// --- Конец функции ---

$this->render('layouts/app-header', ['title' => $title]);

// --- ИСПРАВЛЕНИЕ: Определяем переменные более надежным способом ---
$embedUrl = $activeLesson ? getVideoEmbedUrl($activeLesson['content_url']) : null;
$hasText = $activeLesson && !empty(trim($activeLesson['content_text'] ?? ''));

// Новая, надежная проверка. $isSubmitted будет true, только если $userAnswer - это не пустой массив.
$isSubmitted = !empty($userAnswer);
$isLocked = $isSubmitted && in_array($userAnswer['status'], ['submitted', 'checked']);
// --- Конец исправления ---
?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="course-page-wrapper">

                <div class="lesson-content-area">
                    <div class="lesson-header">
                        <a href="/dashboard" class="back-link">← Назад к курсам</a>
                        <h1 class="lesson-title">
                            <?= htmlspecialchars($course['title']) ?>:
                            <span class="lesson-subtitle"><?= $activeLesson ? htmlspecialchars($activeLesson['title']) : 'Выберите урок' ?></span>
                        </h1>
                    </div>

                    <?php if ($isSubmitted): ?>
                        <?php
                        // Этот код теперь будет выполняться только когда $userAnswer точно существует
                        $statusText = ''; $statusClass = ''; $comment = !empty($userAnswer['comment']) ? htmlspecialchars($userAnswer['comment']) : '';
                        switch ($userAnswer['status']) {
                            case 'checked': $statusText = 'Принято'; $statusClass = 'alert-success'; break;
                            case 'rejected': $statusText = 'Отклонено (можно пересдать)'; $statusClass = 'alert-danger'; break;
                            default: $statusText = 'Отправлено на проверку'; $statusClass = 'alert-info'; break;
                        }
                        ?>
                        <div class="alert <?= $statusClass ?>" style="margin-bottom: 24px;">
                            <span class="alert-icon"><?php if($userAnswer['status'] === 'checked') echo '✅'; elseif($userAnswer['status'] === 'rejected') echo '❌'; else echo 'ℹ️'; ?></span>
                            <div>
                                <p><strong>Статус:</strong> <?= $statusText ?></p>
                                <?php if ($comment): ?>
                                    <p style="margin-top: 8px;"><strong>Комментарий преподавателя:</strong> <?= $comment ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="lesson-viewer">
                        <?php if ($embedUrl): ?>
                            <div class="video-player-responsive">
                                <iframe src="<?= $embedUrl ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; gyroscope; accelerometer; clipboard-write; screen-wake-lock;" allowfullscreen></iframe>
                            </div>
                        <?php elseif ($hasText): ?>
                            <div class="text-content-mockup">
                                <p><?= nl2br(htmlspecialchars($activeLesson['content_text'])) ?></p>
                            </div>
                        <?php else: ?>
                            <p>Контент для этого урока еще не добавлен.</p>
                        <?php endif; ?>
                    </div>

                    <?php if ($embedUrl && $hasText): ?>
                        <div class="lesson-description">
                            <h3>Материалы к уроку</h3>
                            <p><?= nl2br(htmlspecialchars($activeLesson['content_text'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($course['description'])): ?>
                        <div class="lesson-description">
                            <h3>О курсе: <?= htmlspecialchars($course['title']) ?></h3>
                            <p><?= htmlspecialchars($course['description']) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($homework && !empty($homework['questions'])): ?>
                        <div class="lesson-description">
                            <h3>Домашнее задание</h3>
                            <?php
                            $questions = json_decode($homework['questions'], true);
                            $answers = $isSubmitted ? json_decode($userAnswer['answers'], true) : [];
                            ?>
                            <form action="/homework/submit" method="POST">
                                <input type="hidden" name="homework_id" value="<?= $homework['id'] ?>">
                                <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                <input type="hidden" name="lesson_id" value="<?= $activeLesson['id'] ?>">

                                <?php foreach ($questions as $index => $item): ?>
                                    <div class="homework-group">
                                        <label>Вопрос <?= $index + 1 ?>:</label>
                                        <p><?= htmlspecialchars($item['q']) ?></p>
                                        <textarea name="answers[]" placeholder="Ваш ответ..." <?= $isLocked ? 'readonly' : '' ?>><?= $isSubmitted ? htmlspecialchars($answers[$index]['a'] ?? '') : '' ?></textarea>
                                    </div>
                                <?php endforeach; ?>

                                <?php if (!$isLocked): ?>
                                    <button type="submit" class="btn-primary" style="margin-top: 20px;"><?= $isSubmitted ? 'Пересдать на проверку' : 'Сдать на проверку' ?></button>
                                <?php endif; ?>
                            </form>
                        </div>
                    <?php endif; ?>
                    <?php
                    // --- НОВАЯ ЛОГИКА ОТОБРАЖЕНИЯ КНОПКИ ---
                    // Кнопка появляется, только если:
                    // 1. Урок активен.
                    // 2. В уроке НЕТ домашнего задания.
                    // 3. Урок еще НЕ пройден.
                    if ($activeLesson && !$homework && !in_array($activeLesson['id'], $completedLessonIds)):
                        ?>
                        <div class="complete-lesson-wrapper">
                            <form action="/lesson/complete/<?= $activeLesson['id'] ?>" method="POST">
                                <button type="submit" class="btn-primary">Отметить как пройденный</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="course-sidebar">
                    <div class="course-progress">
                        <h4>Прогресс курса</h4>
                        <div class="progress-bar"><div class="progress-bar-fill" style="width: <?= $progressPercentage ?>%;"></div></div>
                        <span><?= $progressPercentage ?>% пройдено</span>
                    </div>
                    <div class="course-content-list">
                        <h4>Содержание курса</h4>
                        <ul>
                            <?php foreach ($course['modules'] as $module): ?>
                                <li class="module-item"><?= htmlspecialchars($module['title']) ?></li>
                                <?php foreach ($module['lessons'] as $lesson): ?>
                                    <a href="/course/<?= $course['id'] ?>/lesson/<?= $lesson['id'] ?>" class="lesson-link">
                                        <li class="lesson-item <?= ($activeLesson && $lesson['id'] === $activeLesson['id']) ? 'active' : '' ?>">
                                            <?php if (in_array($lesson['id'], $completedLessonIds)): ?>
                                                <span class="lesson-completed-icon">✅</span>
                                            <?php endif; ?>
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