<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
    
    class Driver {
        //func to get driver's info
        public static function getDriverData($driverId, $ibDrivers) {
            $arSelect = [
                "ID",
                "NAME",
                "PROPERTY_NAME",
                "PROPERTY_SURNAME",
                "PROPERTY_PHONE",
                "PROPERTY_EMAIL"
            ];

            $arFilter = [
                "IBLOCK_ID" => $ibDrivers,
                "ID"        => $driverId,
                "ACTIVE"    => "Y",
            ];

            $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

            if ($el = $res->fetch()) {
                return [
                    'name'    => $el['PROPERTY_NAME_VALUE'],
                    'surname' => $el['PROPERTY_SURNAME_VALUE'],
                    'phone'   => $el['PROPERTY_PHONE_VALUE'],
                    'email'   => $el['PROPERTY_EMAIL_VALUE'],
                ];
            }
            
            return false;
        }
    }