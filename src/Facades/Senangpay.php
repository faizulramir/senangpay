<?php

namespace Faizulramir\Senangpay\Facades;

use Illuminate\Support\Facades\Facade;

class Senangpay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'senangpay';
    }
}