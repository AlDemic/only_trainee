<?php 
include_once __DIR__ . "/services/csv_reader.php";
include_once __DIR__ . "/services/raw_prop_array.php";
include_once __DIR__ . "/services/prop_validation.php";

\Bitrix\Main\Loader::includeModule('iblock');

class Parser {
    public function run($fileName, $IBLOCK_ID) {
        //create classes
        $reader = new CsvReader();
        $propsRawMaker = new RawPropArray();
        $propsValidMaker  = new PropValidation();

        //common result array
        $result = [
            'ok' => [],
            'errors' => []
        ];

        //get props from bitrix
        $propsArray = $this->getProps($IBLOCK_ID);

        //delete old records from bitrix iblock
        $this->deleteOldRecords($IBLOCK_ID);

        //get rows from csv
        $rows = $reader->readCsv($fileName);

        //validate each row and add to iblock
        foreach($rows as $row) {
            $propsRaw = $propsRawMaker->rawProp($row);

            //text to id for props
            $propsValid = $propsValidMaker->propValidate($propsRaw['props'], $propsArray);

            //add record
            $res = $this->addRecord(
                $propsValid,
                $IBLOCK_ID,
                $propsRaw['fields']['NAME'],
                $propsRaw['fields']['ACTIVE']
            );

            //write log
            if($res['status'] === 'ok') {
                $result['ok'][] = $res['id'];
            } else {
                $result['errors'][] = $res;
            }
        }

        //send result
        return $result;
    }

    private function getProps($IBLOCK_ID) {
        $arProps = []; //common array

        $rsProp = CIBlockPropertyEnum::GetList(
            ["SORT" => "ASC", "VALUE" => "ASC"],
            ['IBLOCK_ID' => $IBLOCK_ID]
        );

        while ($arProp = $rsProp->Fetch()) {
            $key = trim($arProp['VALUE']);
            $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
        }

        return $arProps;
    }

    private function deleteOldRecords($IBLOCK_ID) {
        $rsElements = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);

        while ($element = $rsElements->GetNext()) {
            CIBlockElement::Delete($element['ID']);
        }
    }

    private function addRecord($props, $IBLOCK_ID, $name, $active) {
        global $USER;
        $el = new CIBlockElement;

        $arLoadProductArray = [
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => $IBLOCK_ID,
            "PROPERTY_VALUES" => $props,
            "NAME" => $name,
            "ACTIVE" => $active,
        ];

        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            return [
                'status' => 'ok',
                'id' => $PRODUCT_ID
            ];
        } else {
            return [
                'status' => 'error',
                'error' => $el->LAST_ERROR,
                'data' => $arLoadProductArray
            ];
        }
    }
}


