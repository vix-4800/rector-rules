<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class ArrayLiteralReturn
{
    public function values(): array
    {
        return ['name' => 'Ada'];
    }
}
