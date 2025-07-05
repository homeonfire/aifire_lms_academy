<?php
// Скрипт для инициализации статических страниц
require_once 'src/Config/database.php';
require_once 'src/Models/StaticPage.php';

try {
    $staticPageModel = new StaticPage();
    
    // Проверяем, существуют ли уже страницы
    if (!$staticPageModel->exists('policy')) {
        $policyContent = json_encode([
            'time' => time() * 1000,
            'blocks' => [
                [
                    'id' => 'header1',
                    'type' => 'header',
                    'data' => [
                        'text' => 'Политика конфиденциальности',
                        'level' => 1
                    ]
                ],
                [
                    'id' => 'paragraph1',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Здесь будет размещена политика конфиденциальности.'
                    ]
                ],
                [
                    'id' => 'paragraph2',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Данный документ описывает, как мы собираем, используем и защищаем вашу личную информацию.'
                    ]
                ],
                [
                    'id' => 'header2',
                    'type' => 'header',
                    'data' => [
                        'text' => '1. Сбор информации',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph3',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Мы собираем информацию, которую вы предоставляете при регистрации и использовании нашего сервиса.'
                    ]
                ],
                [
                    'id' => 'header3',
                    'type' => 'header',
                    'data' => [
                        'text' => '2. Использование информации',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph4',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Собранная информация используется для предоставления услуг и улучшения пользовательского опыта.'
                    ]
                ],
                [
                    'id' => 'header4',
                    'type' => 'header',
                    'data' => [
                        'text' => '3. Защита информации',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph5',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Мы принимаем все необходимые меры для защиты вашей личной информации.'
                    ]
                ],
                [
                    'id' => 'header5',
                    'type' => 'header',
                    'data' => [
                        'text' => '4. Контакты',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph6',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'По всем вопросам обращайтесь к нам по email: privacy@example.com'
                    ]
                ]
            ],
            'version' => '2.31.0'
        ]);
        
        $staticPageModel->create('policy', 'Политика конфиденциальности', $policyContent);
        echo "✅ Страница 'Политика конфиденциальности' создана\n";
    } else {
        echo "ℹ️ Страница 'Политика конфиденциальности' уже существует\n";
    }
    
    if (!$staticPageModel->exists('oferta')) {
        $ofertaContent = json_encode([
            'time' => time() * 1000,
            'blocks' => [
                [
                    'id' => 'header1',
                    'type' => 'header',
                    'data' => [
                        'text' => 'Публичная оферта',
                        'level' => 1
                    ]
                ],
                [
                    'id' => 'paragraph1',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Здесь будет размещена публичная оферта.'
                    ]
                ],
                [
                    'id' => 'paragraph2',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Настоящий документ является публичной офертой на заключение договора.'
                    ]
                ],
                [
                    'id' => 'header2',
                    'type' => 'header',
                    'data' => [
                        'text' => '1. Общие положения',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph3',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Данная оферта регулирует отношения между нами и пользователями нашего сервиса.'
                    ]
                ],
                [
                    'id' => 'header3',
                    'type' => 'header',
                    'data' => [
                        'text' => '2. Предмет договора',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph4',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Предметом договора является предоставление образовательных услуг.'
                    ]
                ],
                [
                    'id' => 'header4',
                    'type' => 'header',
                    'data' => [
                        'text' => '3. Права и обязанности сторон',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph5',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Каждая из сторон имеет определенные права и обязанности, описанные в данном документе.'
                    ]
                ],
                [
                    'id' => 'header5',
                    'type' => 'header',
                    'data' => [
                        'text' => '4. Срок действия',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph6',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'Оферта действует бессрочно до момента её отзыва.'
                    ]
                ],
                [
                    'id' => 'header6',
                    'type' => 'header',
                    'data' => [
                        'text' => '5. Контакты',
                        'level' => 2
                    ]
                ],
                [
                    'id' => 'paragraph7',
                    'type' => 'paragraph',
                    'data' => [
                        'text' => 'По всем вопросам обращайтесь к нам по email: legal@example.com'
                    ]
                ]
            ],
            'version' => '2.31.0'
        ]);
        
        $staticPageModel->create('oferta', 'Публичная оферта', $ofertaContent);
        echo "✅ Страница 'Публичная оферта' создана\n";
    } else {
        echo "ℹ️ Страница 'Публичная оферта' уже существует\n";
    }
    
    echo "\n🎉 Инициализация статических страниц завершена!\n";
    echo "📝 Теперь вы можете:\n";
    echo "   - Просматривать страницы по адресам: /policy и /oferta\n";
    echo "   - Редактировать их в админке: /admin/static-pages\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
?> 