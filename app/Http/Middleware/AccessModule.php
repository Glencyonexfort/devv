<?php

namespace App\Http\Middleware;

use App\TenantModules;
use Closure;

class AccessModule
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $module_id)
    {
        $module = TenantModules::where('tenant_id', '=', auth()->user()->tenant_id)->where('sys_module_id', '=', $module_id)->first();

        if ($module)
            return $next($request);

        return redirect(route('admin.inbox'));
    }
}
