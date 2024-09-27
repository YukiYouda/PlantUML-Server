<?php
require __DIR__ . '/vendor/autoload.php';
use function Jawira\PlantUml\encodep;

// POSTリクエストからパラメータを取得
$format = $_POST['format'] ?? 'svg';
$editorContent = $_POST['editorContent'];

// editorの入力値をencode
$encode = encodep($editorContent);


if ($format === 'png') {
    // PNG用のヘッダーを設定
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="downloaded_image.png"');
    $imageData = file_get_contents("http://www.plantuml.com/plantuml/png/{$encode}");
    echo $imageData;
} elseif ($format === 'txt') {
    // テキスト用のヘッダーを設定
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="downloaded_image.txt"');
    $imageData = file_get_contents("http://www.plantuml.com/plantuml/txt/{$encode}");
    echo $imageData;
} else {
    // SVG用のヘッダーを設定
    header('Content-Type: image/svg+xml');
    header('Content-Disposition: attachment; filename="downloaded_image.svg"');

    // URLからSVGデータを取得
    $imageData = file_get_contents("http://www.plantuml.com/plantuml/svg/{$encode}");

    // Content-Length ヘッダーを設定してデータサイズを明示
    header('Content-Length: ' . strlen($imageData));

    // SVGデータをそのまま出力
    echo $imageData;
}
