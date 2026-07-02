<?php
    if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

    require_once __DIR__ . '/classes/Driver.php';
    require_once __DIR__ . '/classes/Car.php';
    require_once __DIR__ . '/classes/Trip.php';
    require_once __DIR__ . '/classes/ErrorParams.php';

    /*
        1. Set correct IDs for position/class comfort for cars
        2. Adjust array for position <-> class
        -> in /classes/Car.php
    */

    class CarRent extends CBitrixComponent {
        
        public function onPrepareComponentParams($arParams)
        {
            //if empty or no choosen
            $arParams["IBLOCK_CAR"] = (int)($arParams["IBLOCK_CAR"] ?? 0);
            $arParams["IBLOCK_DRIVERS"] = (int)($arParams["IBLOCK_DRIVERS"] ?? 0);
            $arParams["HLBLOCK_TRIPS"] = (int)($arParams["HLBLOCK_TRIPS"] ?? 0);
            
            return $arParams;
        }
        
        public function executeComponent()
        {
            $this->initResult();
                     
            $this->includeComponentTemplate();
        }
        
        //main logic
        private function initResult(): void
        {
            $request = \Bitrix\Main\Application::getInstance()
                ->getContext()
                ->getRequest();

            //local params
            $ibCars = $this->arParams['IBLOCK_CAR'];
            $ibDrivers = $this->arParams['IBLOCK_DRIVERS'];
            $hlbTrips = $this->arParams['HLBLOCK_TRIPS'];
            $startTrip = $request->getQuery('start') ?? '';
            $endTrip = $request->getQuery('end') ?? '';
            
            $errMsg = ErrorParams::anyError(
                $ibCars,
                $ibDrivers,
                $hlbTrips,
                $startTrip,
                $endTrip
            );

            if($errMsg !== '') {
                ShowError($errMsg);
                return;
            }

            //IF ALL OK -> make filter and collect cars 
            $this->arResult['CARS'] = $this->getCarsForRent(
                $ibCars,
                $ibDrivers,
                $hlbTrips,
                $startTrip,
                $endTrip
            );
        }

        private function getCarsForRent(
            $ibCars,
            $ibDrivers,
            $hlbTrips,
            $startTrip,
            $endTrip
        ) {

            $allowedCars = Car::allowedCarsPos($ibCars, $ibDrivers);

            $tripsRange = Trip::getTripsRecordsByRange($hlbTrips, $startTrip, $endTrip);

            return Trip::getFreeCars($allowedCars, $tripsRange);
        }
    }

