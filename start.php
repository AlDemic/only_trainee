<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
    LocalRedirect('/');
}

//take csv files list for select form
$files = glob(__DIR__ . '/csv/*.csv');

$fileNames = array_map(function($file) {
    return basename($file);
}, $files);

?>
<html>
    <h1>----Укажите инфоблок(ID) и выберите CSV файл----<br/></h2>

    <form action="controller.php" method="POST">
        <h2>Выберите файл:</h2>

        <select name="file">
            <option value="">Выберите файл</option>
            <?foreach ($fileNames as $file):?>
                <option value="<?=$file?>"><?=$file?></option>
            <?endforeach;?>
        </select>

        <h2>Укажите ID инфоблока:</h2>

        <input type="number" name="iblock" min="1" required />

        <br/>
        <button type="submit">Запустить</button>
    </form>

</html>
