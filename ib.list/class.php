<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    class IbList extends CBitrixComponent {
        
        public function onPrepareComponentParams($arParams)
        {
            //if empty or no choosen
            $arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"] ?? '');
            $arParams["IBLOCK_ID"] = (int)($arParams["IBLOCK_ID"] ?? 0);
            
            return $arParams;
        }
        
        public function executeComponent()
        {
            //save cache
            if ($this->startResultCache())
            {
                $this->initResult();
                
                //if arResult empty -> cancel cache
                if(empty($this->arResult)) {
                    
                    $this->abortResultCache();
                    ShowError('ИБ не найден');
                    
                    //return;
                }
                
                $this->includeComponentTemplate();
            }
        }
        
        //main logic
        private function initResult(): void
        {
            //local params
            $ibType = $this->arParams['IBLOCK_TYPE'];
            $ibId = $this->arParams['IBLOCK_ID'];

            //check if select any type
            if($this->arParams["IBLOCK_TYPE"] === '') {
                ShowError('Тип инфоблока не выбран');
                return;
            }

            //if select type or id -> collect elements
            $this->arResult['ITEMS'] = $this->makeResultArray($ibType, $ibId);           
        }

        //collect elements by type or by id
        private function makeResultArray($ibType, $ibId) {
            //final array
            $arResult = [];

            //if select IB ID
            if(is_int($ibId) && $ibId > 0) {
                $arResult[$ibId] = $this->collectElById($ibId);
                
                return $arResult; //return final array
            }

            //if select ONLY IB TYPE
            //get array with IB IDs by selected IB Type
            $arIds = $this->collectIBTypeIds($ibType);
 
            //collect elements for every IB ID
            foreach($arIds as $id) {
                $arResult[$id] = $this->collectElById($id);
            }

            return $arResult;
        }

        //take IB Type and make array of its IB ids
        private function collectIBTypeIds($ibType) {
            $iblockFilter = [
                'TYPE' => $ibType
            ];

            $ibIds = []; //ids array by selected IB Type
            $db_iblock = CIBlock::GetList(["SORT" => "ASC"], $iblockFilter);
            while($arRes = $db_iblock->fetch()) {
                $ibIds[] = $arRes["ID"]; 
            }

            return $ibIds;
        }

        //take all records by IB ID
        private function collectElById($ibId) {
            //select fields
            $arSelect = ["ID", "NAME", "DATE_ACTIVE_FROM"];

            //FILTERS
            $arFilter = [
                "IBLOCK_ID" => $ibId
            ];

            if ($this->arParams["FILTER_ACTIVE"] === "Y") {
                $arFilter["ACTIVE"] = "Y";
            }

            //final array with elements
            $finalArray = [];

            //get elements by id
            $res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            while($el = $res->fetch()) {
                $finalArray[] = $el;
            }

            return $finalArray;
        }
    }

