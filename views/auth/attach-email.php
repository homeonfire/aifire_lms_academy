<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" action="/attach-email">
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" name="password" required>
    </div>
    <div>
        <label for="confirm_password">Подтвердите пароль:</label>
        <input type="password" name="confirm_password" required>
    </div>
    <button type="submit">Привязать email</button>
</form>