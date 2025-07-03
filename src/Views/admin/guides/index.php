<?php $this->render('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->render('layouts/admin-sidebar'); ?>
        <main class="main-content">
            <div class="content-wrapper">
                <div class="admin-header">
                    <h1 class="page-title">Управление гайдами</h1>
                    <a href="/admin/guides/new" class="btn btn-primary">Добавить гайд</a>
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
                        <?php foreach ($guides as $guide): ?>
                            <tr>
                                <td><?= $guide['id'] ?></td>
                                <td><?= htmlspecialchars($guide['title']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($guide['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="/admin/guides/edit/<?= $guide['id'] ?>" class="btn btn-secondary">Редактировать</a>
                                    <a href="/admin/guides/delete/<?= $guide['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены?');">Удалить</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<?php $this->render('layouts/footer'); ?>