<?php

namespace Dev\Site\Agents;

use Bitrix\Iblock\IblockTable;


class Iblock
{
    //var to find LOG IB id by code
    protected static $ibLogCode = "LOG";

    public static function clearOldLogs()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            //find ib by code
            $iblock = IblockTable::getRow([
                'filter' => [
                    '=CODE' => self::$ibLogCode
                ]
            ]);

            $iblockId = $iblock['ID'];

            //get ID of last 10 record
            $n = 10; 

            $rsSave = \CIBlockElement::GetList(['ID' => 'DESC'], [
                'IBLOCK_ID' => $iblockId
            ], false, [
                'nTopCount' => $n,
            ], ['ID']);

            //save records id to not delete from db
            $saveIds = [];
            while($rsRes = $rsSave->fetch()) {
                $saveIds[] = $rsRes['ID'];
            }

            //get all records and delete all except ids in $saveIds
            $rsAll = \CIBlockElement::GetList([], [
                'IBLOCK_ID' => $iblockId
            ], false, false, ['ID']);

            while ($recId = $rsAll->Fetch()) {
                if(in_array($recId['ID'], $saveIds)) continue;

                //delete if not in $saveIds
                \CIBlockElement::Delete($recId['ID']);
            }
        }

        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
