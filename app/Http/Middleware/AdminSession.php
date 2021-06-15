<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;

use App\Models\Users;

class AdminSession
{
    /**
     * Посредник, ограничивающий неавторизованный доступ в панель управления
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /* Получение куки администратора */
        $adminCookie = Cookie::get(config('constants.adminCookieName'));

        /* Поиск пользователя в Users по полученной строке Куки. Дополнительно через JOIN добавляется название Роли*/
        $adminInfo = Users::where('sessionHash', $adminCookie ?? 'NaN')->join('roles', 'users.roleID', '=', 'roles.id')->select('users.*', 'title as roleTitle')->first();

        if($adminInfo)
        {
            /* Получение полного списка прав пользователя */
            $adminRights = AdminAuthController::userAccess($adminInfo['roleID'], null, true);

            /* При успешной проверке передать в шаблон данные об админе*/
            View::share('adminInfo', $adminInfo);
            View::share('adminRights', $adminRights);

            /* Запись данных в Request Attribute, для повторного доступа и обработки в контроллере*/
            $request->attributes->add(['adminInfo' => $adminInfo]);
            $request->attributes->add(['adminRights' => $adminRights]);

            return $next($request);
        }
        else
        {
            /* Возврат на авторизацию*/
            return redirect('/control/login');
        }
    }
}
