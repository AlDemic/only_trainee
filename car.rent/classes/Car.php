<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
    
    class Car {
        //IDs params for user position(uf) and car comfortable class
        private const POS_JUN = 4;
        private const POS_MID = 5;
        private const POS_SEN = 6;
            
        private const COMF_STD = 18;
        private const COMF_BUS = 19;
        private const COMF_PREM = 20;

        //array for position and comfortable class
        private const POS_COM_AR = [
            self::POS_JUN => [self::COMF_STD],
            self::POS_MID => [self::COMF_STD, self::COMF_BUS],
            self::POS_SEN => [self::COMF_BUS, self::COMF_PREM]
        ];

        public static function allowedCarsPos($ibCars, $ibDrivers) {
            global $USER;
            $user = \Bitrix\Main\UserTable::getList([
                'filter' => ['ID' => $USER->GetID()],
                'select' => ['ID', 'UF_POSITION']
            ])->fetch();

            $userPosition = $user['UF_POSITION'];

            $cars = [];
            $arFilter = [
                "IBLOCK_ID" => $ibCars
            ];

            $arSelect = [
                "ID",
                "NAME",
                "PROPERTY_MODEL",
                "PROPERTY_BRAND",
                "PROPERTY_COMF",
                "PROPERTY_NUMBER",
                "PROPERTY_DRIVER"
            ];

            $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            while ($el = $res->fetch()) {

                //check if can take this car by position
                if(!self::canPosRentCar($el['PROPERTY_COMF_ENUM_ID'], $userPosition)) continue;

                //get driver's data if can take this car
                $driverData = Driver::getDriverData($el['PROPERTY_DRIVER_VALUE'], $ibDrivers);
                if(!$driverData) continue;

                //make array with car info
                $cars[] = [
                    'id' => $el['ID'],
                    'brand' =>  $el['PROPERTY_BRAND_VALUE'],
                    'model' =>  $el['PROPERTY_MODEL_VALUE'],
                    'comfClass' =>  $el['PROPERTY_COMF_VALUE'],
                    'number' =>  $el['PROPERTY_NUMBER_VALUE'],
                    'driver' =>  $driverData,
                ];
                
            }

            return $cars;
        }

        //function to check is user's position can rent car
        private static function canPosRentCar($carClass, $userPosition) {
            $posAr = self::POS_COM_AR[$userPosition];
            //check what car user can rent
            if(in_array($carClass, $posAr)) {
                return true;
            }

            return false;
        }
    }