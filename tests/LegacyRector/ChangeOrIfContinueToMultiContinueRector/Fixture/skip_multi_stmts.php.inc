<?php

namespace Vix\RectorRules\Tests\LegacyRector\ChangeOrIfContinueToMultiContinueRector\Fixture;

class SkipMultiStmts
{
    public function canDrive(Car $newCar)
    {
        foreach ($cars as $car) {
            if ($car->hasWheels() || $car->hasFuel()) {
                executeSideEffect();
                continue;
            }

            $car->setWheel($newCar->wheel);
            $car->setFuel($newCar->fuel);
        }
    }
}

?>
