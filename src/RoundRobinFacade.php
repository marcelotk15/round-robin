<?php

namespace Teka\RoundRobin;

use Illuminate\Support\Facades\Facade;

class RoundRobinFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'round-robin';
    }

}