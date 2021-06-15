<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Admin\AuthController;
use App\Models\RightsInRole;
use Closure;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;

class AdminRoles
{
    /**
     * Посредник проверки доступа к определённой странице или действию.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $right)
    {
        // Получение RoleID из данных о пользователе (AdminSessionMiddleware)
        $adminRole = $request->attributes->get('adminInfo')->roleID;

        // Если есть доступ, то загрузка не прерывается, ИНАЧЕ http 403
        if(AdminAuthController::userAccess($adminRole, $right))
        {
            return $next($request);
        }
        else
        {
            return abort(403);
        }
    }
}
