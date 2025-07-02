<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title">Управление курсами</h1>
                    <a href="/admin/courses/new" class="btn btn-primary">Добавить курс</a>
                </div>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Дата создания</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= $course['id'] ?></td>
                                    <td><?= htmlspecialchars($course['title']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($course['created_at'])) ?></td>
                                    <td class="actions">
                                        <a href="/admin/courses/content/<?= $course['id'] ?>" class="btn btn-primary">Управление</a>
                                        <a href="/admin/courses/edit/<?= $course['id'] ?>" class="btn btn-secondary">Редактировать</a>
                                        <a href="/admin/courses/delete/<?= $course['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот курс?');">Удалить</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">Курсы еще не созданы.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>