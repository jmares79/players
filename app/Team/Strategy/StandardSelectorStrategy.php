<?php

namespace App\Team\Strategy;

use App\Team\Interfaces\TeamSelectorInterface;

class StandardSelectorStrategy implements TeamSelectorInterface
{
    public function select(array $requirements): array
    {
        return [];
    }
}
