<?php
// Задаем заголовок страницы
$title = 'Вход в аккаунт';

// Подключаем шапку
$this->render('layouts/header', ['title' => $title]);
?>

    <div class="auth-container">
        <div class="auth-form-wrapper">
            <div class="auth-header">
                <h1>С возвращением!</h1>
                <p>Войдите в свой аккаунт</p>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'registered'): ?>
                <div class="alert alert-success">
                    Вы успешно зарегистрировались! Теперь можете войти.
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/login" class="auth-form">
                <div class="input-group">
                    <label for="email">Email адрес</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <div class="label-wrapper">
                        <label for="password">Пароль</label>
                        <a href="#" class="forgot-password">Забыли пароль?</a>
                    </div>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn-primary">Войти</button>
            </form>

            <div class="auth-footer">
                <p>Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
            </div>
        </div>
    </div>

<?php
// Подключаем подвал
$this->render('layouts/footer');
?>