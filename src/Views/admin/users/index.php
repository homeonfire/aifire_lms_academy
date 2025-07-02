<?php $this->renderAdminPage('layouts/admin-header', ['title' => $title]); ?>

    <div class="admin-layout">
        <?php $this->renderAdminPage('layouts/admin-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <h1 class="page-title">Управление пользователями</h1>

                <div class="admin-table-container">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Имя</th>
                            <th>Роль</th>
                            <th>Дата регистрации</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td><?= $user['role'] === 'admin' ? 'Администратор' : 'Пользователь' ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td class="actions">
                                        <?php if ($_SESSION['user']['id'] != $user['id']): ?>
                                            <button type="button" class="btn btn-sm btn-secondary edit-user-btn" data-user-id="<?= $user['id'] ?>">Редактировать</button>
                                            <form action="/admin/users/change-role" method="POST" class="role-change-form">
                                            </form>
                                        <?php else: ?>
                                            <span>(Это вы)</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Пользователи не найдены.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <div id="edit-user-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Редактировать пользователя</h2>
                    <button type="button" class="close-modal-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="edit-user-form" action="" method="POST" class="admin-form">
                        <input type="hidden" name="user_id" id="edit-user-id">

                        <div class="input-group">
                            <label for="edit-first-name">Имя</label>
                            <input type="text" id="edit-first-name" name="first_name">
                        </div>
                        <div class="input-group">
                            <label for="edit-last-name">Фамилия</label>
                            <input type="text" id="edit-last-name" name="last_name">
                        </div>
                        <div class="input-group">
                            <label for="edit-email">Email</label>
                            <input type="email" id="edit-email" name="email" disabled>
                        </div>
                        <hr style="border-color: #e0e0e0; border-top: 0; margin: 25px 0;">
                        <div class="input-group">
                            <label for="edit-role">Роль</label>
                            <select id="edit-role" name="role">
                                <option value="user">Пользователь</option>
                                <option value="admin">Администратор</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="edit-experience-level">Уровень</label>
                            <select id="edit-experience-level" name="experience_level">
                                <option value="beginner">Начинающий</option>
                                <option value="intermediate">Средний</option>
                                <option value="advanced">Продвинутый</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="edit-preferred-skill-type">Интересующие навыки</label>
                            <input type="text" id="edit-preferred-skill-type" name="preferred_skill_type">
                        </div>

                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php $this->renderAdminPage('layouts/footer'); ?>