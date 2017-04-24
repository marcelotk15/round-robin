<?php
/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 23/04/2017
 * Time: 20:33
 */

namespace Laravel\RoundRobin;


use Illuminate\Support\Facades\Facade;

class RoundRobinFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'round-robin';
    }

}