<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    use Bitrix\Main\Loader;

    if (!Loader::includeModule('iblock')) return;

    //get IB types
    $arTypesEx = CIBlockParameters::GetIBlockTypes();

    //get IB ids
    $arIBlocks = [];
    $iblockFilter = [ //base filter
        'ACTIVE' => 'Y',
    ];

    //filter to show IB by selected IB TYPE
    if (!empty($arCurrentValues['IBLOCK_TYPE']))
    {
        $iblockFilter['TYPE'] = $arCurrentValues['IBLOCK_TYPE'];
    }

    //collect IB by filter
    $db_iblock = CIBlock::GetList(["SORT"=>"ASC"], $iblockFilter);
    while($arRes = $db_iblock->fetch()) {
        $arIBlocks[$arRes["ID"]] = "[" . $arRes["ID"] . "] " . $arRes["NAME"];
    }

    $arComponentParameters = [
        'GROUPS' => [],
        'PARAMETERS' => [
            "IBLOCK_TYPE" => [
                "PARENT" => "BASE",
                "NAME" => "Выберите тип инфоблока",
                "TYPE" => "LIST",
                "VALUES" => $arTypesEx,
                "REFRESH" => "Y",
            ],
            "IBLOCK_ID" => [
                "PARENT" => "BASE",
                "NAME" => "Выберите ID инфоблока(необязательно)",
                "TYPE" => "LIST",
                "VALUES" => $arIBlocks,
                "REFRESH" => "Y",
                "ADDITIONAL_VALUES" => "Y",
            ],
            "FILTER_ACTIVE" => [
                "PARENT" => "BASE",
                "NAME" => "Только активные",
                "TYPE" => "CHECKBOX",
                "DEFAULT" => "Y",
            ],  
        ],
    ];

