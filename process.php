<?php
require __DIR__ . '/vendor/autoload.php';
use function Jawira\PlantUml\encodep;

if (isset($_POST['text'])) {
    $inputText = $_POST['text'];
    $encode = encodep($inputText);
    echo "http://www.plantuml.com/plantuml/svg/{$encode}";
}