<?php include_once "tpl/academy-header.php"; ?>

    <section class="lesson-section">
        <div class="full-width-video">
            <div class="video-full-container">
                <?php
                foreach ($lesson->content as $element) {
                    switch ($element['type']) {
                        case 'video':
                            echo '<div class="video-element">';
                            echo '<iframe src="' . htmlspecialchars($element['content']) . '" width="560" height="315" frameborder="0" allowfullscreen></iframe>';
                            echo '</div>';
                            break;
                    }
                }
                ?>
                <div class="video-overlay"></div>
            </div>
            <div class="lesson-card-top">
                <h1 class="lesson-name"><?php echo htmlspecialchars($lesson->title); ?></h1>
                <p class="lesson-name">Урок <?php echo htmlspecialchars($lesson->order_number); ?></p>
            </div>
        </div>
        <div class="container">
            <div class="lesson-container">

                <div class="lesson-content">
                    <?php
                    // Отображаем элементы урока
                    foreach ($lesson->content as $element) {
                        switch ($element['type']) {
                            case 'text':
                                echo '<div class="text-element">';
                                echo '<p>' . htmlspecialchars($element['content']) . '</p>';
                                echo '</div>';
                                break;
                            case 'link':
                                echo '<div class="link-element">';
                                echo '<a href="' . htmlspecialchars($element['url']) . '" target="_blank">' . htmlspecialchars($element['content']) . '</a>';
                                echo '</div>';
                                break;
                            case 'heading':
                                echo '<div class="heading-element">';
                                echo '<h2>' . htmlspecialchars($element['content']) . '</h2>';
                                echo '</div>';
                                break;
                            case 'subheading':
                                echo '<div class="subheading-element">';
                                echo '<h3>' . htmlspecialchars($element['content']) . '</h3>';
                                echo '</div>';
                                break;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Находим блок video-overlay
        const videoOverlay = document.querySelector('.video-overlay');

        // Находим блок full-width-video
        const fullWidthVideo = document.querySelector('.full-width-video');

        // Добавляем обработчик клика
        if (videoOverlay && fullWidthVideo) {
            videoOverlay.addEventListener('click', function() {
                // Переключаем класс expanded
                fullWidthVideo.classList.toggle('expanded');
            });
        }
    });
</script>
<?php include_once "tpl/academy-footer.php"; ?>