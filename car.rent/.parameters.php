<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock\HighloadBlockTable;


    if (!Loader::includeModule('iblock')) return;
    if (!Loader::includeModule('highloadblock')) return;

    /* !!!
        Cars only IB
        Drivers only IB
        Trips only highload block
    */

    //COLLECT IB
    $iblockFilter = [ //base filter
        'ACTIVE' => 'Y',
    ];

    $arIBlocksCars = []; 
    $db_iblockCars = CIBlock::GetList(["SORT"=>"ASC"], $iblockFilter);
    while($arRes = $db_iblockCars->fetch()) {
        $arIBlocksCars[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
    }

    $arIBlocksDrivers = [];
    $db_iblockDrivers = CIBlock::GetList(["SORT"=>"ASC"], $iblockFilter);
    while($arRes = $db_iblockDrivers->fetch()) {
        $arIBlocksDrivers[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
    }

    //COLLECT HLBLOCKS
    $arHLBlocks = [];
    $db_hlblock = HighloadBlockTable::getList(['order' => ['NAME' => 'ASC']]);

    while ($hlBlock = $db_hlblock->fetch()) {
        $arHLBlocks[$hlBlock['ID']] = '[' . $hlBlock['ID'] . '] ' . $hlBlock['NAME'];
    }

    $arComponentParameters = [
        'GROUPS' => [],
        'PARAMETERS' => [
            "IBLOCK_CAR" => [
                "PARENT" => "BASE",
                "NAME" => "Выберите инфоблок автомобилей",
                "TYPE" => "LIST",
                "VALUES" => $arIBlocksCars,
                "REFRESH" => "Y",
            ],
            "IBLOCK_DRIVERS" => [
                "PARENT" => "BASE",
                "NAME" => "Выберите инфоблок водителей",
                "TYPE" => "LIST",
                "VALUES" => $arIBlocksDrivers,
                "REFRESH" => "Y",
            ],
            "HLBLOCK_TRIPS" => [
                "PARENT" => "BASE",
                "NAME" => "Выберите хай-лоад блок поездок",
                "TYPE" => "LIST",
                "VALUES" => $arHLBlocks,
                "REFRESH" => "Y",
                "ADDITIONAL_VALUES" => "Y",
            ], 
        ],
    ];

