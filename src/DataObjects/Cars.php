<?php

namespace App\DataObjects;

use Pimcore\Model\DataObject\Car;
use Pimcore\Model\Element\Service;

class Cars
{
    private $car = null;

    public function __construct(array $data, array $header)
    {
        $key = Service::getValidKey($data['ID'], 'object');
        $car = Car::getByPath('/' . $key);

        if (!$car) {
            $car = new Car();
            $car->setParentId(1);
            $car->setKey($key);
        }

        $this->car = $car;
        $this->setGeneralInfo($data, $header);
        $this->car->save();
        $this->car->clearDependentCache();
    }

    private function setGeneralInfo(array $data, array $header): void
    {
        foreach ($header as $key) {
            $this->car->setValue($key, $data[$key]);
        }
    }

    public function getCar(): Car
    {
        return $this->car;
    }
}