<?php 
    require_once $_SERVER["DOCUMENT_ROOT"] . "/local/modules/dev.site/include.php";

    //event global
    $eventManager = \Bitrix\Main\EventManager::getInstance(); 

    //reg event for OnAfterIBlockElementAdd
    $eventManager->addEventHandler(
        "iblock",
        "OnAfterIBlockElementAdd",
        array(
            "Dev\\Site\\Handlers\\Iblock",
            "addLog"
        )
    );

    //reg event for OnAfterIBlockElementUpdate
    $eventManager->addEventHandler(
        "iblock",
        "OnAfterIBlockElementUpdate",
        array(
            "Dev\\Site\\Handlers\\Iblock",
            "addLog"
        )
    );

