<?php include_once "tpl/academy-header.php"; ?>
    <section class="profile-page">
        <div class="container">
            <h3 style="text-align: center; font-weight: 900">AI Fire LMS</h3>

            <!-- Сообщения об ошибках и успехах -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="color: red; text-align: center; margin-bottom: 20px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" style="color: green; text-align: center; margin-bottom: 20px;">
                    <?= $success ?>
                </div>
            <?php endif; ?>

            <h1>Профиль пользователя</h1>

            <!-- Информация о пользователе -->
            <div class="profile-info" style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
                <div style="margin-bottom: 15px;">
                    <strong>Email:</strong> <?= htmlspecialchars($user->email) ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Telegram:</strong>
                    <?php if ($user->telegram_id): ?>
                        Привязан (ID: <?= $user->telegram_id ?>)
                    <?php else: ?>
                        Не привязан
                    <?php endif; ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Имя:</strong> <?= htmlspecialchars($user->first_name) ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Фамилия:</strong> <?= htmlspecialchars($user->last_name) ?>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>Аватар:</strong>
                    <?php if ($user->avatar): ?>
                        <img src="<?= htmlspecialchars($user->avatar) ?>" alt="Аватар" style="width: 100px; height: 100px; border-radius: 50%;">
                    <?php else: ?>
                        Аватар не установлен
                    <?php endif; ?>
                </div>
            </div>

            <!-- Привязка Telegram -->
            <div style="text-align: center; margin-top: 30px;">
                <?php if (!$user->telegram_id): ?>
                    <h2>Привязать Telegram</h2>
                    <div class="telegram-button">
                        <script src="https://telegram.org/js/telegram-widget.js?21"
                                data-telegram-login="skillspro_academy_bot"
                                data-size="large"
                                data-auth-url="/attach-telegram"
                                data-request-access="write"></script>
                    </div>
                <?php else: ?>
                    <p>Telegram уже привязан.</p>
                <?php endif; ?>
            </div>

            <!-- Ссылка на выход -->
            <div style="text-align: center; margin-top: 20px;">
                <a href="/logout" style="color: #007bff;">Выйти из аккаунта</a>
            </div>
        </div>
    </section>
<?php include_once "tpl/academy-footer.php"; ?>