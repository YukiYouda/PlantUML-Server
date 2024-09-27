<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlantUML-Server</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.33.0/min/vs/loader.js"></script>
</head>

<body>
    <a href="problems.php">UML問題集</a>
    <div id="wrapper" style="display: flex;">
        <div id="container" style="width:700px;height:600px;border:1px solid #ccc;"></div>
        <div id="result" style="width:700px;height:600px;border:1px solid #ccc;"></div>
            <form action="download.php" method="post">
                <textarea id="hiddenInput" name="editorContent" style="display:none;"></textarea>
                <label for="format">Download Format:</label>
                <select id="format" name="format">
                    <option value="png">png</option>
                    <option value="svg">svg</option>
                    <option value="txt">txt</option>
                </select>
                <button type="submit" onclick="submitForm()">Download</button>
            </form>
    </div>
    <script>
        require.config({
            paths: {
                'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.33.0/min/vs'
            }
        });
        require(['vs/editor/editor.main'], function() {
            editor = monaco.editor.create(document.getElementById('container'), {
                value: '',
                language: 'plaintext'
            });

            // エディタの内容が変更されたときの処理
            editor.onDidChangeModelContent(function() {
                const content = editor.getValue();

                // AJAXでPHPに内容を送信
                fetch('process.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'text=' + encodeURIComponent(content)
                    })
                    .then(response => response.text())
                    .then(encodedContent => {
                        // <img>要素を作成して表示
                        const resultContainer = document.getElementById('result');
                        resultContainer.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = encodedContent;
                        resultContainer.appendChild(img);
                    })
                    .catch(error => console.error('Error:', error));
            });
        });

        // ダウンロードボタンが押された時の処理
        function submitForm() {
            // Monaco Editorの内容を取得
            const editorContent = editor.getValue();

             // hiddenInputに値を設定
            document.getElementById('hiddenInput').value = editorContent;

             // フォームを送信
            form.submit();
        }
    </script>
</body>

</html>