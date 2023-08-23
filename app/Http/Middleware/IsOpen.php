<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;

class IsOpen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $setting = Setting::find(1);
        if ($setting != null && $setting->is_open == 1) {
            return $next($request);
        }

        return redirect('/shop-closed');
    }
}
