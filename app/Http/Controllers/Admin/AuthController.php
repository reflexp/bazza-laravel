<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use App\Models\Users;
use App\Models\Rights;
use App\Models\Roles;
use App\Models\RightsInRole;

class AuthController extends Controller
{

    public function __construct() {}

    public function loginPage()
    {
        return view('Admin.pages.login.login');
    }

    public function login(Request $request)
    {
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);
        
        $validateRules = [
            'login' => ['required', 'string', 'min:3', 'max:64'],
            'password' => ['required', 'string', 'min:6', 'max:64'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Очистка номера телефона от лишних символов и приведение в формат +77771112233
        $phone = RequesterController::clearPhone($request->login);

        // Выборка пользователей, которые не удалены и активны
        $user = Users::where('login', $phone)->where('active', 1)->where('deleted', 0);
        $userInfo = $user->first();

        // Возврат ошибки, если пользователь не найден
        if (!$userInfo) return ['ok' => false, 'result' => ['code' => '0404', 'message' => config('messagecodes.0404')]];

        // Проверка хэшей пароля с правильностью
        $password = Hash::check($request->password, $userInfo->password);

        if ($password)
        {
            // Если установлен чек "запомни меня на месяц"
            if (isset($request->rememberMe))
            {
                // Получение времени жизни Auth куки для Админа
                $adminCookieSessionKeepAlive = config('constants.adminCookieSessionKeepAlive');
            }
            else {
                // Время жизни Auth куки - до закрытия браузера
                $adminCookieSessionKeepAlive = null;
            }
            // Генерация уникального хэша сессии
            $adminCookieName = config('constants.adminCookieName');
            $newSession = Hash::make(time() . 'fat231');

            // Обновление хэша сессии у пользователя
            $user->update(['sessionHash' => $newSession]);

            // Установка куки файла пользователю
            Cookie::queue($adminCookieName, $newSession, $adminCookieSessionKeepAlive);

            return ['ok' => true, 'result' => []];
        }
        else
        {
            return ['ok' => false, 'result' => ['code' => '0401', 'message' => config('messagecodes.0401')]];
        }

    }
    public function logout(Request $request)
    {
        // Генерация уникального хэша сессии
        $adminCookieName = config('constants.adminCookieName');

        // Очистка куки пользователя
        // Редирект на страницу авторизации
        return redirect(route('Admin.login'))->withCookie(Cookie::forget($adminCookieName));

    }

    public static function userAccess($roleID, $right = null, $all = false) {

        /* Получение всех Правил в Роли, JOIN для вывода RightName */
        $rightsInRole = RightsInRole::where('roleID', $roleID)->join('rights', 'rights_in_role.rightID', '=', 'rights.id')->get();    // Правила в Роли

        /* Список доступных правил для модулей */
        $userAccess = [
            'nomenclature' => false,
            'suppliers' => false,
            'users' => false,
            'clients' => false,
            'storages' => false,
            'statistics' => false,
            'settings' => false,
            'chat' => false,
            'static' => false,
            'logistics' => false,
            'orders' => false,
            'ordersbundles' => false,
            'payments' => false,
        ];
        /* Если правило есть в списке Правил Роли, то в массиве Правил изменяется значение на True */
        foreach ($rightsInRole as $userRight) //$rightsInRole
        {
            $userAccess[$userRight['name']] = true;
        }

        /** Если установлен параметр @param $all, то возвращается весь массив */
        /** ИНАЧЕ возвращается только зачение одного заправиваемого правила @param $right */

        if ($all)
            return $userAccess; /** @return array() */
        else
            return $userAccess[$right]; /** return true/false */
    }


}
