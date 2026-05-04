<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");


include_once __DIR__ . "/parser.php";

class ParserController {
    public function prepare() {
        $file = (string)($_POST['file'] ?? '');
        $iblockId = (int)($_POST['iblock'] ?? 0);

        if($file === '' || $iblockId <= 0) {
            echo "Нужно выбрать инфоблок и файл.";
            return;
        }

        $parser = new Parser();
        $result = $parser->run($file, $iblockId);

        //show result
        echo "Добавлено:" . count($result['ok']) . " записей успешно <br/>";

        echo "Ошибки: <br/>";
        foreach($result['errors'] as $error) {
            echo "Описание: " . $error['error'] . "<br/>";
            echo "Детали свойств: <br/>";
            echo "Название: " . $error['data']['NAME'] . "<br/>";
            echo "Активность: " . $error['data']['ACTIVE'] . "<br/>";
            echo "-------------------<br/>";
        }
    } 

}

$controller = new ParserController();
$controller->prepare();


