<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeOrIfContinueToMultiContinueRector\Fixture;

class SkipNotContinue
{
    public function canDrive(Car $newCar)
    {
        foreach ($cars as $car) {
            if ($car->hasWheels() || $car->hasFuel()) {
                break;
            }

            $car->setWheel($newCar->wheel);
            $car->setFuel($newCar->fuel);
        }
    }
}

?>
