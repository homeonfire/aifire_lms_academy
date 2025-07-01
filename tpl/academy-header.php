<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="/assets/styles/academy-styles.css?v=1" type="text/css">
    <!-- -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/link@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest"></script>
</head>
<body>
<nav class="sidebar">
    <button class="toggle-btn">
        <span class="toggle-icon">‹</span>
        <span class="text">Главное меню</span>
    </button>

    <ul class="menu-items">
        <li class="menu-item">
            <div class="highlight-bar"></div>
            <div class="menu-header" data-title="Courses">
                <span class="icon">✦</span>
                <span class="text">Курсы</span>
            </div>
            <ul class="submenu">
                <li class="submenu-item active" onclick="openPage('/courses')">
                    <span class="icon">→</span>
                    <span class="text">Мои курсы</span>
                </li>
                <li class="submenu-item">
                    <span class="icon">→</span>
                    <span class="text">Домашние задания</span>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <div class="highlight-bar"></div>
            <div class="menu-header" data-title="Profile">
                <span class="icon">⭗</span>
                <span class="text">Профиль</span>
            </div>
            <ul class="submenu">
                <li class="submenu-item">
                    <span class="icon">→</span>
                    <span class="text">Настройки</span>
                </li>
                <li class="submenu-item">
                    <span class="icon">→</span>
                    <span class="text">Мои покупки</span>
                </li>
            </ul>
        </li>
        <?php if ($_SESSION['role']=='admin'): ?>
        <li class="menu-item">
            <div class="highlight-bar"></div>
            <div class="menu-header" data-title="Profile">
                <span class="icon">⭗</span>
                <span class="text">Пользователи</span>
            </div>
            <ul class="submenu">
                <li class="submenu-item">
                    <span class="icon">→</span>
                    <span class="text">Все пользователи</span>
                </li>
            </ul>
        </li>
            <li class="menu-item">
                <div class="highlight-bar"></div>
                <div class="menu-header" data-title="Profile">
                    <span class="icon">⭗</span>
                    <span class="text">Рассылки</span>
                </div>
                <ul class="submenu">
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">E-mail рассылки</span>
                    </li>
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">Telegram рассылки</span>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <div class="highlight-bar"></div>
                <div class="menu-header" data-title="Profile">
                    <span class="icon">⭗</span>
                    <span class="text">Настройки</span>
                </div>
                <ul class="submenu">
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">Telegram</span>
                    </li>
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">E-mail</span>
                    </li>
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">Платежные системы</span>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <div class="highlight-bar"></div>
                <div class="menu-header" data-title="Profile">
                    <span class="icon">⭗</span>
                    <span class="text">Документация</span>
                </div>
                <ul class="submenu">
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">Пользовательская</span>
                    </li>
                    <li class="submenu-item">
                        <span class="icon">→</span>
                        <span class="text">Админская</span>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<script>
    function openPage(page){
        location.href = page;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.querySelector('.toggle-btn');

        // Toggle sidebar
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            document.body.style.paddingLeft = sidebar.classList.contains('collapsed') ? '80px' : '280px';
        });

        // Accordion logic
        document.querySelectorAll('.menu-header').forEach(header => {
            header.addEventListener('click', async () => {
                const submenu = header.nextElementSibling;
                const isCollapsed = sidebar.classList.contains('collapsed');

                if(isCollapsed) {
                    sidebar.classList.remove('collapsed');
                    document.body.style.paddingLeft = '280px';
                    await new Promise(r => setTimeout(r, 400));
                }

                header.classList.toggle('active');
                submenu.classList.toggle('active');

                document.querySelectorAll('.menu-header, .submenu').forEach(el => {
                    if(el !== header && el !== submenu) {
                        el.classList.remove('active');
                    }
                });
            });
        });

        // Submenu items interaction
        document.querySelectorAll('.submenu-item').forEach(item => {
            item.addEventListener('click', () => {
                document.querySelectorAll('.submenu-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            });
        });
    });
</script>
<section class="academy">
    <div class="window_block">