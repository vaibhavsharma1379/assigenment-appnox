<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
   
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null :'http://127.0.0.1:8000';
    }
}
