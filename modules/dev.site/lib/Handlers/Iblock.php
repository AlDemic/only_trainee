<?php

namespace Dev\Site\Handlers;

\Bitrix\Main\Loader::includeModule('iblock');

use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\IblockTable;

class Iblock
{
    //to check by id
    protected static $ibLogCode = "LOG";

    public static function addLog($arFields)
    {
        //check if IB not LOG
        $ibLog = IblockTable::getRow([
            'filter' => [
                '=CODE' => self::$ibLogCode
            ]
        ]);

        $ibLogId = $ibLog['ID'];

        if($arFields['IBLOCK_ID'] == $ibLogId) return; //return if event from LOG IB element

        //get section ID from LOG IB
        //check if exist section or create
        $logSectionId = self::logSectionId($ibLogId, $arFields['IBLOCK_ID']);

        //check RESULT field to understand update or added event
        switch($arFields['RESULT']) {
            //after added
            case (is_int($arFields['RESULT']) && $arFields['RESULT'] > 0):
                self::OnAfterIBlockElementAddHandler($arFields, $ibLogId, $logSectionId);
                break;
            //after updated
            case is_bool($arFields['RESULT']):
                self::OnAfterIBlockElementUpdateHandler($arFields, $ibLogId, $logSectionId);
                break;
            default:
                return;
        }
    }
    
    //main func for after added new
    private static function OnAfterIBlockElementAddHandler($arFields, $ibLogId, $logSectionId) {
        //str for preview text
        $previewStr = self::makePreviewText($arFields['NAME'], $arFields['IBLOCK_ID'], $arFields['IBLOCK_SECTION'][0]);

        //add record
        self::addElement($arFields['ID'], $ibLogId, $logSectionId, $previewStr);
    }

    //main func for after updated
    private static function OnAfterIBlockElementUpdateHandler($arFields, $ibLogId, $logSectionId) {
        //str for preview text
        $previewStr = self::makePreviewText($arFields['NAME'], $arFields['IBLOCK_ID'], $arFields['IBLOCK_SECTION'][0]);

        //add record
        self::addElement($arFields['ID'], $ibLogId, $logSectionId, $previewStr);
    }

    //get section ID in LOG IB
    private static function logSectionId($ibLogId, $elIbId) {
        //get name and code of element's id
        $ibData = IblockTable::getList([
            'select' => ['NAME', 'CODE'],
            'filter' => ['=ID' => $elIbId]
        ])->fetch();

        //check if exist
        $section = SectionTable::getRow([
            'filter' => [
                'IBLOCK_ID' => $ibLogId,
                '=NAME' => $ibData['NAME'],
                '=CODE' => $ibData['CODE']
            ]
        ]);

        //if no have -> create
        if($section == false) {
            $result = SectionTable::add([
                'IBLOCK_ID' => $ibLogId,
                'NAME' => $ibData['NAME'],
                'CODE' => $ibData['CODE']
            ]);

            if($result->isSuccess()) {
                //return section ID
                return $result->getId();
            }
        }

        return $section['ID'];
    }

    //make str like: ib name -> names of all sections -> el name
    private static function makePreviewText($nameEl, $idIb, $idSectionFinal) {
        $ibName = IblockTable::getList([
            'select' => ['NAME'],
            'filter' => ['=ID' => $idIb]
        ])->fetch()['NAME'];

        $secStr = ' -> ';

        //get all sections to last IBLOCK_SECTION from arFields
        $rsPath = \CIBlockSection::GetNavChain($idIb, $idSectionFinal, ['NAME']);

        while($res = $rsPath->fetch()) {
            $secStr .= $res['NAME'] . ' -> ';
        }

        //return str
        return $ibName . $secStr . $nameEl;
    }

    private static function addElement($name, $ibId, $secId, $previewStr) {
        //create record
        $el = new \CIBlockElement;

        $el->Add([
            'IBLOCK_ID' => $ibId,
            'IBLOCK_SECTION_ID' => $secId,
            'NAME' => $name,
            'PREVIEW_TEXT' => $previewStr,
            'ACTIVE' => 'Y',
            'ACTIVE_FROM' => date('d.m.Y')
        ]);
    }

}
