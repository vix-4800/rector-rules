<?php

declare(strict_types=1);

namespace Vix\RectorRules\Tests\LegacyRector\AddReturnDocblockForDimFetchArrayFromAssignsRector\Fixture;

final class ExistingArrayReturnDocblock
{
    /**
     * @return array<string, string>
     */
    public function values(): array
    {
        $values = [];
        $values['name'] = 'Ada';

        return $values;
    }
}
