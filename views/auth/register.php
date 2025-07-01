<?php include_once "tpl/header.php"; ?>
    <section class="register-page">
        <div class="container">
            <h3 style="text-align: center; font-weight: 900">AI Fire LMS</h3>

            <!-- Сообщения об ошибках -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger" style="color: red; text-align: center; margin-bottom: 20px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <h1>Регистрация по email</h1>
            <form method="POST" action="/register" style="max-width: 400px; margin: 0 auto;">
                <div style="margin-bottom: 15px;">
                    <label for="email" style="display: block; margin-bottom: 5px;">Email:</label>
                    <input type="email" name="email" id="email" required style="width: 100%; padding: 10px; font-size: 16px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="password" style="display: block; margin-bottom: 5px;">Пароль:</label>
                    <input type="password" name="password" id="password" required style="width: 100%; padding: 10px; font-size: 16px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="confirm_password" style="display: block; margin-bottom: 5px;">Подтвердите пароль:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required style="width: 100%; padding: 10px; font-size: 16px;">
                </div>
                <div style="text-align: center;">
                    <button type="submit" style="padding: 10px 20px; font-size: 16px; background: #007bff; color: #fff; border: none; cursor: pointer;">
                        Зарегистрироваться
                    </button>
                </div>
            </form>

            <!-- Ссылка на вход -->
            <div style="text-align: center; margin-top: 20px;">
                <p>Уже есть аккаунт? <a href="/login" style="color: #007bff;">Войдите</a></p>
            </div>
        </div>
    </section>
<?php include_once "tpl/footer.php"; ?>