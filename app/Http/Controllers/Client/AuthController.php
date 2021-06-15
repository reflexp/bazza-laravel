<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use App\Rules\PhoneValidation;

use App\Models\Clients;
use App\Models\Messages;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function signupPage()
    {
        return view('Client.pages.account.signup');
    }

    public function loginPage()
    {
        return view('Client.pages.account.login');
    }

    public function resetPage()
    {
        return view('Client.pages.account.reset-password');
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
        $client = Clients::where('login', $phone)->where('active', 1)->where('deleted', 0);
        $clientInfo = $client->first();

        // Возврат ошибки, если пользователь не найден
        if (!$clientInfo) return ['ok' => false, 'result' => ['code' => '0404', 'message' => config('messagecodes.0404')]];

        // Проверка хэшей пароля с правильностью
        $password = Hash::check($request->password, $clientInfo->password);

        if ($password)
        {
            // Если установлен чек "запомнить меня"
            if (isset($request->rememberMe))
            {
                // Получение времени жизни Auth куки для Клиента
                $clientCookieSessionKeepAlive = config('constants.clientCookieSessionKeepAlive');
            }
            else {
                // Время жизни Auth куки - до закрытия браузера
                $clientCookieSessionKeepAlive = null;
            }
            // Генерация уникального хэша сессии
            $clientCookieName = config('constants.clientCookieName');
            $newSession = Hash::make(time() . 'fat231');

            // Обновление хэша сессии у пользователя
            $client->update(['sessionHash' => $newSession]);

            // Установка куки файла пользователю
            Cookie::queue($clientCookieName, $newSession, $clientCookieSessionKeepAlive);

            return ['ok' => true, 'result' => []];
        }
        else
        {
            return ['ok' => false, 'result' => ['code' => '0401', 'message' => config('messagecodes.0401')]];
        }

    }

    public function signup(Request $request) {
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'name' => ['required', 'string', 'max:128'],
            'login' => ['required', 'string', new PhoneValidation, 'unique:clients', 'min:3', 'max:64'],
            'email' => ['required', 'email', 'max:64', 'unique:clients'],
            'buyerType' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:6', 'max:64'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);
        
        // Библиотека времени
        $date = new Carbon;

        // Записывание данных пользователя в клиенты
        $client = new Clients;

        $client->name = $request->name;
        $client->login = $request->login;
        $client->email = $request->email;
        $client->buyerType = $request->buyerType;
        $client->password = Hash::make($request->password);
        $client->active = 0;
        
        $client->save();

        // Генерация кода длинною в 4 символа
        $verificationCode = mt_rand(1000, 9999);
        $hash = Hash::make($verificationCode);
        
        // Записывание данных о сообщении
        $message = new Messages;

        $message->message = 'Bazza.kz ваш код: '.$verificationCode;
        $message->userID = $client->id;
        $message->sendedTo = $request->login;
        $message->sendedToEmail = $request->email;
        $message->type = 1;
        $message->hash = $hash;
        $message->sended = 1;
        $message->sendedTime = $date->today()->format('Y-m-d H:i:s');

        $message->save();

        // Отправка смс сообщения пользователю
        RequesterController::sendSMSMessage($request->login, 'Bazza.kz, ваш код: '.$verificationCode);

        return ['ok' => true, 'result' => ['hash' => $hash]];
    }

    public function logout(Request $request)
    {
        // Генерация уникального хэша сессии
        $clientCookieName = config('constants.clientCookieName');

        // Редирект на страницу авторизации и очистка куки пользователя
        return redirect(route('Client.index'))->withCookie(Cookie::forget($clientCookieName));
    }

    public function verify(Request $request) {
        $validateRules = [
            'hash' => ['required', 'string'],
            'verificationCode' => ['required', 'string', 'min:4'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Извлекаем данные по номеру телефона, если тип 1, то это регистрация
        $message = Messages::where('hash', $request->hash)
            ->where('type', 1)
            ->first(['hash', 'attemptCount', 'id', 'userID']);

        if ($message) {
            // Количество попыток 4 на введение кода
            if ($message->attemptCount < 4) {
                // Проверка введенного кода и хеша в базе данных
                $verify = Hash::check($request->verificationCode, $message->hash);

                // Если данные совпали создает сессию и активирует аккаунт
                if ($verify) {
                    // Время жизни Auth куки - до закрытия браузера
                    $clientCookieSessionKeepAlive = null;
                    // Генерация уникального хэша сессии
                    $clientCookieName = config('constants.clientCookieName');
                    $newSession = Hash::make(time() . 'fat231');
                    // Активация аккаунта клиента в базе данных
                    $client = Clients::where('id', $message->userID)->update(['active' => 1, 'sessionHash' => $newSession]);
                    // Установка куки файла пользователю
                    Cookie::queue($clientCookieName, $newSession, $clientCookieSessionKeepAlive);

                    return ['ok' => true, 'result' => []];
                } else {
                    // Инкрементация попытки в случае неправильного ввода
                    $message->update(['attemptCount' => $message->attemptCount + 1]);

                    return ['ok' => false, 'result' => ['message' => 'Неправильный код']];
                }

            } else {
                return ['ok' => false, 'result' => ['message' => 'Вы превысили количество попыток (4)']];
            }
        } else {
            return ['ok' => false, 'result' => []];
        }
    }

    public function reset(Request $request) {
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'login' => ['required', 'string'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Библиотека времени
        $date = new Carbon;

        // Извлекаем данные по номеру телефона, если тип 2 при сбросе пароля
        $message = Messages::where('sendedTo', $request->login)
            ->where('type', 2)
            ->first(['hash', 'id']);

        $client = Clients::where('login', $request->login)->first();

        if ($client) {
            // Генерация кода длинною в 4 символа
            $verificationCode = mt_rand(1000, 9999);
            $hash = Hash::make($verificationCode);
            
            // Записывание данных о сообщении или обновление
            if (!$message) {
                $message = new Messages;

                $message->message = 'Bazza.kz ваш код: '.$verificationCode;
                $message->userID = $client->id;
                $message->sendedTo = $client->login;
                $message->sendedToEmail = $client->email;
                $message->type = 2;
                $message->hash = $hash;
                $message->sended = 1;
                $message->sendedTime = $date->today()->format('Y-m-d H:i:s');

                $message->save();
            } else {
                $message->message = 'Bazza.kz ваш код: '.$verificationCode;
                $message->hash = $hash;
                $message->attemptCount = 0;
                $message->sendedTime = $date->today()->format('Y-m-d H:i:s');

                $message->update();
            }
            
            // Отправка смс сообщения пользователю
            RequesterController::sendSMSMessage($request->login, 'Bazza.kz, ваш код: '.$verificationCode);

            return ['ok' => true, 'result' => ['hash' => $hash]];
        } else {
            return ['ok' => false, 'result' => ['message' => 'Данный пользователь не найден']];
        }
    }

    public function verifyReset(Request $request) {
        $validateRules = [
            'hash' => ['required', 'string'],
            'verificationCode' => ['required', 'string', 'min:4'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Извлекаем данные по номеру хешу, если тип 2, то это сброс пароля
        $message = Messages::where('hash', $request->hash)
            ->where('type', 2)
            ->first(['hash', 'attemptCount', 'id', 'userID']);

        if ($message) {
            // Количество попыток 4 на введение кода
            if ($message->attemptCount < 4) {
                // Проверка введенного кода и хеша в базе данных
                $verify = Hash::check($request->verificationCode, $message->hash);

                // Если данные совпали то отправляет статус что все хорошо и активирует модалку с изменением пароля
                if ($verify) {
                    return ['ok' => true, 'result' => ['hash' => $message->hash]];
                } else {
                    // Инкрементация попытки в случае неправильного ввода
                    $message->update(['attemptCount' => $message->attemptCount + 1]);

                    return ['ok' => false, 'result' => ['message' => 'Неправильный код']];
                }

            } else {
                return ['ok' => false, 'result' => ['message' => 'Вы превысили количество попыток (4)']];
            }
        } else {
            return ['ok' => false, 'result' => []];
        }
    }

    public function editPasswordReset(Request $request) {
        $validateRules = [
            'hash' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:6', 'max:64'],
            'newPasswordRepeated' => ['required', 'string', 'min:6', 'max:64'],
        ];
    
        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Извлекаем данные по номеру хешу, если тип 2, то это сброс пароля
        $message = Messages::where('hash', $request->hash)
            ->where('type', 2)
            ->first(['userID']);

        if ($message) {
            // Проверка совпадения новых паролей
            if ($request->newPassword == $request->newPasswordRepeated) {

                $client = Clients::where('id', $message->userID)->update(['password' => Hash::make($request->newPassword)]);

                return ['ok' => true, 'result' => ['code' => '0401', 'message' => 'Пароль успешно изменен']];

            } else {
                return ['ok' => false, 'result' => ['code' => '0401', 'message' => 'Новые пароли не совпадают']];
            }
        } else {
            return ['ok' => false, 'result' => []];
        }
    }
}