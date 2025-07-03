// editor-init.js (версия с подробным логированием)

function initializeEditor() {
    console.log("✔️ [Шаг 3] Вызвана функция initializeEditor(). Все зависимости на месте.");

    const editorHolder = document.getElementById('editorjs');
    if (!editorHolder) {
        console.error("❌ КРИТИЧЕСКАЯ ОШИБКА: Элемент #editorjs не найден на странице!");
        return;
    }
    console.log("✔️ [Шаг 4] Элемент #editorjs найден.");

    try {
        console.log("⏳ [Шаг 5] Пытаюсь создать экземпляр EditorJS...");
        const editor = new EditorJS({
            holder: 'editorjs',
            tools: {
                header: Header,
                list: List
            },
            data: window.editorData || {}
        });
        console.log("✅ [Шаг 6] Экземпляр EditorJS успешно создан!");

        const form = document.getElementById('content-form');
        const output = document.getElementById('content_json_output');

        form.addEventListener('submit', function(event) {
            console.log("🚀 [Событие] Нажата кнопка 'Сохранить'.");
            event.preventDefault();
            editor.save().then((outputData) => {
                console.log("✔️ Данные из редактора успешно получены:", outputData);
                output.value = JSON.stringify(outputData);
                console.log("✔️ JSON сохранен в скрытое поле. Отправляю форму...");
                form.submit();
            }).catch((error) => {
                console.error('❌ Ошибка сохранения данных из редактора: ', error);
                alert('Ошибка сохранения контента!');
            });
        });

    } catch (e) {
        console.error("❌ КРИТИЧЕСКАЯ ОШИБКА при инициализации EditorJS: ", e);
    }
}

function dependencyChecker() {
    console.log("---");
    console.log("⏳ [Шаг 2] Проверяю зависимости...");

    // Проверяем каждую зависимость отдельно и логируем ее статус
    const editorJsDefined = typeof EditorJS !== 'undefined';
    const headerDefined = typeof Header !== 'undefined';
    const listDefined = typeof List !== 'undefined';

    console.log(`- EditorJS: ${editorJsDefined ? '✅ Найден' : '❌ Не найден'}`);
    console.log(`- Header: ${headerDefined ? '✅ Найден' : '❌ Не найден'}`);
    console.log(`- List: ${listDefined ? '✅ Найден' : '❌ Не найден'}`);

    if (editorJsDefined && headerDefined && listDefined) {
        // Если все на месте, запускаем наш основной код
        initializeEditor();
    } else {
        // Если чего-то не хватает, проверяем снова через 200 миллисекунд
        console.log("...жду 200мс и пробую снова...");
        setTimeout(dependencyChecker, 200);
    }
}

// --- НАЧАЛО ВЫПОЛНЕНИЯ СКРИПТА ---
console.log("▶️ [Шаг 1] Скрипт editor-init.js запущен.");
dependencyChecker();