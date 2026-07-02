<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock\HighloadBlockTable;

    if (!Loader::includeModule('highloadblock')) return;
    if (!Loader::includeModule('iblock')) return;

    class ErrorParams {
        //func to get trips' records in range
        public static function anyError(
            $ibCars,
            $ibDrivers,
            $hlbTrips,
            $startTrip,
            $endTrip
        ) {
            $errMsg = '';

            if($ibCars == 0 || $hlbTrips == 0 || $ibDrivers == 0) {
                $errMsg = 'Неверно выбран ИБ или Хай-Лоад блок';
            }

            //check if exist blocks in db
            if(!CIBlock::GetByID($ibCars)->fetch()) {
                $errMsg = 'ИБ автомобилей не существует';
            }

            if(!CIBlock::GetByID($ibDrivers)->fetch()) {
                $errMsg = 'ИБ водителей не существует';
            }

            if(!HighloadBlockTable::GetByID($hlbTrips)->fetch()) {
                $errMsg = 'Хай-Лоад блок поездок не существует';
            }

            //check if give time
            if($startTrip === '' || $endTrip === '') {
                $errMsg = 'Неверно выбрано время начала или окончания поездки.';
            }

            return $errMsg;
        }
    }