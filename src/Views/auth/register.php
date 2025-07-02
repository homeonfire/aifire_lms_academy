<?php
$title = 'Регистрация';
$this->render('layouts/header', ['title' => $title]);
?>

    <div class="auth-container">
        <div class="auth-form-wrapper">
            <div class="auth-header">
                <h1>Создать аккаунт</h1>
                <p>Присоединяйтесь к нашему сообществу</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" class="auth-form">
                <div class="input-group">
                    <label for="email">Email адрес</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Пароль</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="input-group">
                    <label for="password_confirm">Подтвердите пароль</label>
                    <input type="password" name="password_confirm" id="password_confirm" required>
                </div>
                <button type="submit" class="btn-primary">Зарегистрироваться</button>
            </form>

            <div class="auth-footer">
                <p>Уже есть аккаунт? <a href="/login">Войти</a></p>
            </div>
        </div>
    </div>

<?php
$this->render('layouts/footer');
?>