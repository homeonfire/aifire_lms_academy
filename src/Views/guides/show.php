<?php
// aifire_lms_academy/src/Views/guides/show.php
$this->render('layouts/app-header', ['title' => $title]);
$embedUrl = getVideoEmbedUrl($guide['content_url']);

// Проверяем наличие контента Editor.js
$hasEditorJsContent = !empty(trim($guide['content_json'] ?? '')) && ($guide['content_json'] !== '{}');
?>

    <div class="app-layout">
        <?php $this->render('layouts/app-sidebar'); ?>

        <main class="main-content">
            <div class="content-wrapper">
                <div class="lesson-header">
                    <a href="/guides" class="back-link">← Назад ко всем гайдам</a>
                    <h1 class="lesson-title"><?= htmlspecialchars($guide['title']) ?></h1>
                </div>

                <div class="guide-content-area">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger" style="margin-bottom: 24px;">
                            <?= htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success" style="margin-bottom: 24px;">
                            <?= htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($embedUrl): ?>
                        <div class="video-player-responsive" style="margin-bottom: 24px;">
                            <iframe src="<?= $embedUrl ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture; encrypted-media" allowfullscreen></iframe>
                        </div>
                    <?php endif; ?>

                    <?php if ($hasEditorJsContent): ?>
                        <div class="lesson-description" style="margin-top: 20px;">
                            <h3>Содержание гайда</h3>
                            <div id="editorjs-viewer" class="editorjs-viewer-container"></div>
                        </div>
                    <?php elseif (!$embedUrl): /* Если нет видео и нет контента Editor.js */ ?>
                        <div class="lesson-description" style="margin-top: 20px;">
                            <p>Контент для этого гайда еще не добавлен.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="/public/assets/editor.js" defer></script>
    <script src="/public/assets/header.js" defer></script>
    <script src="/public/assets/list.js" defer></script>
    <script>
        // Данные для Editor.js (парсим JSON-строку)
        const editorJsData = <?= !empty($guide['content_json']) ? $guide['content_json'] : '{}' ?>;

        // Функция для инициализации Editor.js в режиме только для чтения
        function initializeEditorViewer() {
            const editorViewerHolder = document.getElementById('editorjs-viewer');
            if (editorViewerHolder && Object.keys(editorJsData).length > 0) {
                try {
                    const editorViewer = new EditorJS({
                        holder: 'editorjs-viewer',
                        readOnly: true, // Включаем режим только для чтения
                        data: editorJsData,
                        minHeight: 100, // Минимальная высота для просмотра
                        tools: { // Обязательно перечислите все используемые блоки, даже в режиме readOnly
                            header: Header,
                            list: EditorjsList,
                            // Если используете ImageTool, добавьте его сюда
                            // image: ImageTool
                        }
                    });
                    console.log("Editor.js viewer initialized successfully for guide.");
                } catch (e) {
                    console.error("Error initializing Editor.js viewer for guide:", e);
                    editorViewerHolder.style.display = 'none';
                }
            } else if (editorViewerHolder) {
                editorViewerHolder.style.display = 'none';
                console.log("No Editor.js content to display for guide, hiding container.");
            }
        }

        window.addEventListener('load', () => {
            initializeEditorViewer();
        });
    </script>
<?php $this->render('layouts/footer'); ?>