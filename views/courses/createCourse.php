<?php include_once "tpl/academy-header.php"; ?>
    <section class="header main-page">
        <div class="container">
            <div class="row">
                <div class="heading">
                    <h1 class="banner-heading">Создание курса</h1>
                </div>
            </div>

        </div>
    </section>
    <section>
        <div id="editorjs"></div>
        <script>

            const editor = new EditorJS({
                tools: {
                    list: EditorjsList,
                    autofocus: true
                }
            });

        </script>
    </section>
<?php include_once "tpl/academy-footer.php"; ?>