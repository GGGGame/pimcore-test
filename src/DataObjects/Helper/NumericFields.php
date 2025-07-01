<?php

namespace App\DataObjects\Helper;

class NumericFields
{
    public static function get(): array
    {
        return [
            'Make',
            'Model',
            'BaseModel',
            'Year',
            'MFRCode',
            'Drive',
            'Transmission',
            'TransmissionDescriptor',
            'EngineDescriptor',
            'VehicleSizeClass',
            'ATVType',
            'ElectricMotor',
            'StartStop',
            'FuelType1',
            'FuelType2',
            'TCharger',
            'SCharger',
            'C240Dscr',
            'C240BDscr',
            'MPGData'
        ];
    }
}