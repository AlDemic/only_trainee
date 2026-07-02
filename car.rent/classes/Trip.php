<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    use Bitrix\Main\Loader;
    use Bitrix\Highloadblock\HighloadBlockTable;

    if (!Loader::includeModule('highloadblock')) return;

    class Trip {
        //func to get trips' records in range
        public static function getTripsRecordsByRange($hlbTrips, $start, $end) {
            $hlblock = HighloadBlockTable::getById($hlbTrips)->fetch();

            $entity = HighloadBlockTable::compileEntity($hlblock);
            $entityClass = $entity->getDataClass();

            $startBitrix = new \Bitrix\Main\Type\DateTime($startTrip);
            $endBitrix = new \Bitrix\Main\Type\DateTime($endTrip);

            $arSelect = ['ID', 'UF_CAR', 'UF_DATE_START', 'UF_DATE_END'];
            $arFilter = [
                '<UF_DATE_START' => $endBitrix,
                '>UF_DATE_END'   => $startBitrix,
            ];

            $res = $entityClass::getList([
                'select' => $arSelect,
                'filter' => $arFilter,
            ]);

            $trips = [];
            while ($el = $res->fetch()) {
                $trips[] = $el;
            }

            return $trips;
        }

        //func to make final free car for user
        public static function getFreeCars($cars, $trips) {
            if(is_array($trips) && empty($trips)) return $cars; //if no have any trips by current time

            $freeCars = [];
            foreach($cars as $car) {
                foreach($trips as $trip) {
                    if($car['id'] == $trip['UF_CAR']) continue;

                    $freeCars[] = $car;
                }
            }

            return $freeCars;
        }
    }