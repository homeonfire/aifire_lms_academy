<?php include_once "tpl/header.php"; ?>
    <section class="login-page">
        <div class="container">
            <h3 style="text-align: center; font-weight: 900">AI Fire LMS</h3>

            <!-- Сообщения об ошибках -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="color: red; text-align: center; margin-bottom: 20px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <h1>Войти при помощи Telegram</h1>
            <div class="telegram-button">
                <script src="https://telegram.org/js/telegram-widget.js?21"
                        data-telegram-login="<?= $telegram_bot_id ?>"
                        data-size="large"
                        data-auth-url="/login"
                        data-request-access="write"></script>
            </div>

            <!-- Разделитель -->
            <div style="text-align: center; margin: 20px 0;">
                <span style="background: #fff; padding: 0 10px;">или</span>
                <hr style="border-top: 1px solid #ccc; margin-top: -12px;">
            </div>

            <!-- Форма входа по email и паролю -->
            <h1>Войти по email</h1>
            <form method="POST" action="/login" style="max-width: 400px; margin: 0 auto;">
                <div style="margin-bottom: 15px;">
                    <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
                    <input type="email" name="email" id="email" required style="width: 100%; padding: 10px; font-size: 16px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password" style="display: block; margin-bottom: 5px;">Пароль:</label>
                    <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; font-size: 16px;">
                </div>
                <div style="text-align: center;">
                    <button type="submit" style="padding: 10px 20px; font-size: 16px; background: #007bff; color: #fff; border: none; cursor: pointer;">
                        Войти
                    </button>
                </div>
            </form>

            <!-- Ссылка на регистрацию -->
            <div style="text-align: center; margin-top: 20px;">
                <p>Нет аккаунта? <a href="/register" style="color: #007bff;">Зарегистрируйтесь</a></p>
            </div>
        </div>
    </section>
<?php include_once "tpl/footer.php"; ?>