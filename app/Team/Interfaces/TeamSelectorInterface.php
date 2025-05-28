<?php

namespace App\Team\Interfaces;

interface TeamSelectorInterface
{
    public function select(array $requirements): array;
}
