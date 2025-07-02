<?php $this->render('layouts/app-header', ['title' => $title]); ?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <h1 class="page-title">Работы на проверку (<?= count($submissions) ?>)</h1>

                <div class="admin-table-container-dark">
                    <table class="admin-table-dark">
                        <thead>
                        <tr>
                            <th>Студент</th>
                            <th>Курс / Урок</th>
                            <th>Дата сдачи</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($submissions)): ?>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?= htmlspecialchars($submission['user_email']) ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($submission['course_title']) ?></strong><br>
                                        <small><?= htmlspecialchars($submission['lesson_title']) ?></small>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($submission['submitted_at'])) ?></td>
                                    <td class="actions">
                                        <a href="/homework-check/<?= $submission['id'] ?>" class="btn btn-primary">Проверить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Новых работ на проверку нет.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>