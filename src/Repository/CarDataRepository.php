<?php

namespace App\Repository;

use Pimcore\Model\DataObject\Car;

class CarDataRepository
{
    public static function getCar(Car $car): array
    {
        return [
            'id' => $car->getKey(),
            'make' => $car->getMake(),
            'model' => $car->getModel(),
            'baseModel' => $car->getBaseModel(),
            'year' => $car->getYear(),
            'fuelType' => $car->getFuelType1() ?? $car->getFuelType2() ?? $car->getFuelType3() ?? null,
            'Cylinders' => $car->getCylinders(),
            'Drive' => $car->getDrive(),
            'Transmission' => $car->getTransmission(),
            'VehicleSizeClass' => $car->getVehicleSizeClass(),
            'AnnualFuelCost' => $car->getAnnualFuelCostForFuelType1() ?? $car->getAnnualFuelCostForFuelType2(),
            'MPG' => [
                'city' => $car->getCityMpgForFuelType1() ?? $car->getCityMpgForFuelType2(),
                'highway' => $car->getHighwayMpgForFuelType1() ?? $car->getHighwayMpgForFuelType2(),
                'combined' => $car->getCombinedMpgForFuelType1() ?? $car->getCombinedMpgForFuelType2(),
            ],
            'Emissions' => $car->getCo2FuelType1() ?? $car->getCo2FuelType2(),
            'Range' => [
                'city' => $car->getRangeCityForFuelType1(),
                'highway' => $car->getRangeHighwayForFuelType1(),
            ],
            'CreatedOn' => $car->getCreationDate(),
            'ModifiedOn' => $car->getModificationDate(),
        ];
    }

    public static function getCars(array $cars): array 
    {
        $result = [];
        foreach ($cars as $car) {
            $result[] = [
                'id' => $car->getKey(),
                'name' => $car->getMake(),
                'model' => $car->getModel(),
            ];
        }

        return $result;
    }
}