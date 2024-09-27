<?php
    require __DIR__ . '/vendor/autoload.php';
    use function Jawira\PlantUml\encodep;

    // JSONファイルを読み込む
    $jsonFile = 'data.json';
    if (file_exists($jsonFile)) {
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
    } else {
        echo json_encode(['error' => 'JSONファイルが見つかりません']);
    }

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['id'] == $id) {
                $title = $data[$i]['title'];
                $uml = $data[$i]['uml'];
            }
        }

        // umlをエンコードする
        $encode = encodep($uml);
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UML問題集</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.33.0/min/vs/loader.js"></script>
    <style>
        table {
            width: 60%;
            border-collapse: collapse;
            margin: 20px auto;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['id'])): ?>
        <h2><?php echo "ID: " . $id . " " . $title ?></h2>
        <div id="wrapper" style="display: flex;">
            <div id="container" style="width:700px;height:600px;border:1px solid #ccc;"></div>
            <div id="result" style="width:700px;height:600px;border:1px solid #ccc;"></div>
            <div id="answer" style="width:700px;height:600px;border:1px solid #ccc;">
                <div id="button" style="margin: 20px 20px;">
                    <button id="uml" onclick="handleClick(this)">Answer UML</button>
                    <button id="code" onclick="handleClick(this)">Answer Code</button>
                </div>
                <div id="umlOrCode">
                    <img src='<?php echo "http://www.plantuml.com/plantuml/svg/{$encode}" ?>' alt="">
                </div>
            </div>
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

            // ボタンをクリックした際に作動する関数
            function handleClick(button) {
                const buttonId = button.id;
                const div = document.getElementById('umlOrCode');

                if (buttonId == "uml") {
                    div.innerHTML = `<img src='<?php echo "http://www.plantuml.com/plantuml/svg/{$encode}" ?>' alt="">`;
                } else {
                    div.innerHTML = `<p><?php echo nl2br(htmlspecialchars($uml, ENT_QUOTES, 'UTF-8')); ?></p>`;
                }
            }
        </script>
    <?php else: ?>
        <table id="jsonTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Theme</th>
                </tr>
            </thead>
            <tbody id="tableBody"></tbody>
        </table>

        <script>
            // JSONファイルを読み込む関数
            async function loadJSON() {
                try {
                    const response = await fetch('data.json');
                    const data = await response.json();
                    generateTable(data);
                } catch (error) {
                    console.error('JSONファイルの読み込みに失敗しました:', error);
                }
            }

            // テーブルを生成する関数
            function generateTable(data) {
                if (data.length == 0) return;

                // テーブルボディを作成
                const tableBody = document.getElementById('tableBody');
                const headers = Object.keys(data[0]);

                for (let i = 0; i < data.length; i++) {
                    const row = document.createElement('tr');

                    // 行をクリックしたときに problems.php?id=ID のように遷移するようにする
                    row.addEventListener('click', () => {
                        window.location.href = `problems.php?id=${data[i][headers[0]]}`;
                    });


                    for (let j = 0; j < 3; j++) {
                        const cell = document.createElement('td');
                        cell.textContent = data[i][headers[j]];
                        row.appendChild(cell);
                    }
                    tableBody.appendChild(row);
                }
            }
            // JSONファイルを読み込む
            loadJSON();
        </script>
    <?php endif; ?>
</body>

</html>