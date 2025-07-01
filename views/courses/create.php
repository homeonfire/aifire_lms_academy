<?php include_once "tpl/academy-header.php"; ?>
<section>
    <div class="container">
        <div class="constructor">
            <h1>Создание урока</h1>
            <input type="hidden" id="module-id" value="<?php echo $module_id; ?>">
            <div>
                <label for="lesson-title">Название урока:</label>
                <input type="text" id="lesson-title" placeholder="Введите название урока">
            </div>
            <div>
                <label for="lesson-order">Порядковый номер урока:</label>
                <input type="number" id="lesson-order" placeholder="Введите номер урока">
            </div>
            <div id="lesson-elements">
                <!-- Здесь будут отображаться добавленные элементы -->
            </div>
            <form id="add-element-form">
                <select id="element-type">
                    <option value="video">Видео</option>
                    <option value="text">Текст</option>
                    <option value="link">Ссылка</option>
                    <option value="link-button">Кнопка</option>
                    <option value="heading">Заголовок</option>
                    <option value="subheading">Подзаголовок</option>
                    <option value="homework">Домашнее задание</option>
                </select>
                <input type="text" id="element-content" placeholder="Введите текст или ссылку">
                <input type="text" id="element-url" placeholder="Введите URL (только для ссылки)" style="display: none;">
                <button type="submit">Добавить элемент</button>
            </form>

            <!-- Homework block -->
            <div id="homework-block" style="display:none; margin: 10px 0;">
                <h4>Вопросы домашнего задания</h4>
                <div id="homework-questions"></div>
                <div style="margin:10px 0;">
                    <input type="text" id="homework-question-text" placeholder="Введите текст вопроса">
                    <select id="homework-question-type">
                        <option value="text">Текстовый ответ</option>
                        <option value="choice">Выбор ответа</option>
                        <option value="file">Загрузка файла</option>
                    </select>
                    <button type="button" id="add-homework-question">Добавить вопрос</button>
                </div>
            </div>
            <!-- End Homework block -->

            <button id="save-lesson">Сохранить урок</button>
        </div>
        <div id="lesson-preview">
            <h2>Предпросмотр урока</h2>
            <div id="preview-content"></div>
        </div>
    </div>
</section>
<script type="text/javascript">
    // Для домашних заданий
    let homeworkQuestions = [];

    document.getElementById('element-type').addEventListener('change', function() {
        const urlInput = document.getElementById('element-url');
        if (this.value === 'link' || this.value === 'link-button') {
            urlInput.style.display = 'block';
        } else {
            urlInput.style.display = 'none';
        }
        document.getElementById('element-content').style.display =
            (this.value !== 'homework') ? 'inline-block' : 'none';
        document.getElementById('homework-block').style.display =
            (this.value === 'homework') ? 'block' : 'none';
    });

    // Добавление вопросов к домашнему заданию
    document.getElementById('add-homework-question').addEventListener('click', function() {
        const questionText = document.getElementById('homework-question-text').value;
        const questionType = document.getElementById('homework-question-type').value;
        if (!questionText) return;
        homeworkQuestions.push({ text: questionText, type: questionType });
        renderHomeworkQuestions();
        document.getElementById('homework-question-text').value = '';
    });
    function renderHomeworkQuestions() {
        const block = document.getElementById('homework-questions');
        block.innerHTML = '';
        homeworkQuestions.forEach((q, i) => {
            const div = document.createElement('div');
            div.textContent = `${i+1}. ${q.text} [${q.type}]`;
            block.appendChild(div);
        });
    }

    // Добавление элементов урока
    document.getElementById('add-element-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const type = document.getElementById('element-type').value;
        if (type === 'homework') {
            if (homeworkQuestions.length === 0) {
                alert('Добавьте хотя бы один вопрос для домашнего задания');
                return;
            }
            const element = { type: 'homework', questions: [...homeworkQuestions] };
            addElementToLesson(element);
            updatePreview(element);
            homeworkQuestions = [];
            renderHomeworkQuestions();
        } else {
            const content = document.getElementById('element-content').value;
            const url = (type === 'link' || type === 'link-button') ? document.getElementById('element-url').value : null;
            if (content) {
                const element = { type, content, url };
                addElementToLesson(element);
                updatePreview(element);
                document.getElementById('element-content').value = '';
                if (url) document.getElementById('element-url').value = '';
            }
        }
    });

    function addElementToLesson(element) {
        const lessonElements = document.getElementById('lesson-elements');
        const elementDiv = document.createElement('div');
        elementDiv.className = 'lesson-element';
        if (element.type === 'homework') {
            elementDiv.textContent = `Домашнее задание: ${element.questions.length} вопросов`;
            // Для простоты, можно хранить вопросы внутри data-атрибута
            elementDiv.dataset.questions = JSON.stringify(element.questions);
        } else {
            elementDiv.textContent = `${element.type}: ${element.content}${element.url ? ` (${element.url})` : ''}`;
        }
        lessonElements.appendChild(elementDiv);
    }

    function updatePreview(element) {
        const previewContent = document.getElementById('preview-content');
        const elementDiv = document.createElement('div');

        if (element.type === 'video') {
            const iframe = document.createElement('iframe');
            iframe.src = element.content;
            iframe.width = '560';
            iframe.height = '315';
            iframe.frameBorder = '0';
            iframe.allowFullscreen = true;
            elementDiv.appendChild(iframe);
        } else if (element.type === 'text') {
            elementDiv.textContent = element.content;
        } else if (element.type === 'link') {
            const link = document.createElement('a');
            link.href = element.url;
            link.textContent = element.content;
            link.target = '_blank';
            elementDiv.appendChild(link);
        } else if (element.type === 'heading') {
            const heading = document.createElement('h2');
            heading.textContent = element.content;
            elementDiv.appendChild(heading);
        } else if (element.type === 'subheading') {
            const subheading = document.createElement('h3');
            subheading.textContent = element.content;
            elementDiv.appendChild(subheading);
        } else if (element.type === 'homework') {
            elementDiv.innerHTML = '<strong>Домашнее задание:</strong><ul>' +
                element.questions.map(q => `<li>${q.text} [${q.type}]</li>`).join('') +
                '</ul>';
        }

        previewContent.appendChild(elementDiv);
    }

    document.getElementById('save-lesson').addEventListener('click', function() {
        const moduleId = document.getElementById('module-id').value;
        const lessonTitle = document.getElementById('lesson-title').value;
        const lessonOrder = document.getElementById('lesson-order').value;
        const lessonElements = document.querySelectorAll('.lesson-element');
        const lessonData = [];

        if (!lessonTitle) {
            alert('Пожалуйста, введите название урока.');
            return;
        }

        lessonElements.forEach(element => {
            if (element.textContent.startsWith('Домашнее задание:')) {
                // Получаем вопросы из data-атрибута
                lessonData.push({
                    type: 'homework',
                    questions: JSON.parse(element.dataset.questions || '[]')
                });
            } else {
                const text = element.textContent.split(': ');
                const type = text[0];
                const content = text[1] ? text[1].split(' (')[0] : '';
                const url = (type === 'link' || type === 'link-button') && text[1] && text[1].includes('(')
                    ? text[1].match(/\((.*?)\)/)[1]
                    : null;

                lessonData.push({
                    type,
                    content,
                    url
                });
            }
        });

        const lesson = {
            module_id: moduleId,
            title: lessonTitle,
            order_number: lessonOrder,
            elements: lessonData
        };

        console.log(JSON.stringify(lesson));

        fetch('/courses/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(lesson)
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка сети');
                }
                return response.json();
            })
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при сохранении урока.');
            });
    });
</script>
<?php include_once "tpl/academy-footer.php"; ?>
