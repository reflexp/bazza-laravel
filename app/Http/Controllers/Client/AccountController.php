<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use App\Models\Orderproducts;
use App\Models\Storages;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

use App\Rules\PhoneValidation;
use App\Models\Clients;
use App\Models\Orders;
use App\Models\Messages;
use Carbon\Carbon;

class AccountController extends Controller
{
    function indexPage() {
        return view('Client.pages.account.index');
    }

    function ordersPage(Request $request) {
        $cartCookieName = config('constants.clientCookieCartName');
        $cartHash = Cookie::get($cartCookieName);

        $orders = $this->getOrders($request);

//        dd(json_decode(json_encode($orders),true));
        return view('Client.pages.account.orders', ['orders' => $orders]);
    }

    function chatPage() {
        return view('Client.pages.account.chat');
    }

    public function editAccount(Request $request) {
        $clientID = $request->attributes->get('clientInfo')['id'];

        $validateRules = [
            'name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', Rule::unique('clients')->ignore($clientID), 'max:64'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $client = Clients::find($clientID);

        $client->name = $request->name;
        $client->email = $request->email;

        $client->update();

        return ['ok' => true, 'result' => []];
    }

    public function getOrders($request) {
        /* Вывод закзазов*/
        $orders = new Orders;
        $getOrderStatuses = config('statuscodes');
        $ordersArray = [];

        $clientID = $request->attributes->get('clientInfo')['id'];

        $ordersInfo = Orders::where('clientID', $clientID)
            ->join('storages', 'orders.deliveryToStorageID', 'storages.id')
            ->select(
                'orders.id',
                'orders.totalSum',
                'orders.comment',
                'orders.deliveryType',
                'orders.deliveryAddressInCity',
                'orders.needPrePayment',
                'orders.paymentConfirmed',
                'orders.userPaymentSum',
                'orders.status',
                'orders.created_at',
                'storages.title as storageTitle'
            )
            ->get();
        foreach ($ordersInfo as $order)
        {
            $orderProducts = Orderproducts::where('orderID', $order['id'])
                ->join('nomenclatures', 'orderproducts.nomenclatureID', 'nomenclatures.id' )
                ->select(
                    'orderproducts.price',
                    'orderproducts.amount',
                    'orderproducts.totalPrice',
                    'orderproducts.status',
                    'nomenclatures.price as nomenclaturePrice',
                    'nomenclatures.title as title',
                    'nomenclatures.article as article',
                    'nomenclatures.manufacturer as manufacturer',
                    'nomenclatures.additionText as additionText'
                )
//                ->exclude(['password', 'sessionHash', 'updated_at', 'created_at'])
                ->get();

            $productsArray = [];
            foreach ($orderProducts as $orderProduct)
            {
                $orderProduct['status'] = [
                    'id' => $orderProduct['status'],
                    'color' => $getOrderStatuses['product'][$orderProduct['status']]['color'],
                    'text' => $getOrderStatuses['product'][$orderProduct['status']]['text'],
                ];
                array_push($productsArray, $orderProduct);
            }

            $order['status'] = [
                'id' => $order['status'],
                'color' => $getOrderStatuses['order'][$order['status']]['color'],
                'text' => $getOrderStatuses['order'][$order['status']]['text'],
            ];
            $order['products'] = $productsArray;

            array_push($ordersArray, $order);
        }

        return $ordersArray;
}
    public function editPassword(Request $request) {
        $validateRules = [
            'oldPassword' => ['required', 'string', 'min:6', 'max:64'],
            'newPassword' => ['required', 'string', 'min:6', 'max:64'],
            'newPasswordRepeated' => ['required', 'string', 'min:6', 'max:64'],
        ];
    
        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Библиотека времени
        $date = new Carbon;
    
        // Данные клиента
        $client = $request->attributes->get('clientInfo');

        // Поиск нужного пользователя
        $client = Clients::find($client->id);

        // Проверка старого пароля с существующим в базе данных
        $password = Hash::check($request->oldPassword, $client->password);

        if ($password) {
            // Проверка совпадает ли старый парль и новый
            if ($request->newPassword == $request->oldPassword) {
                return ['ok' => false, 'result' => ['code' => '0401', 'message' => 'Старый и новый пароль не должны совпадать']];
            }

            // Проверка совпадения новых паролей
            if ($request->newPassword == $request->newPasswordRepeated) {

                $client->update(['password' => Hash::make($request->newPassword)]);

                return ['ok' => true, 'result' => ['code' => '0401', 'message' => 'Пароль успешно изменен']];

            } else {
                return ['ok' => false, 'result' => ['code' => '0401', 'message' => 'Новые пароли не совпадают']];
            }

        } else {
            return ['ok' => false, 'result' => ['code' => '0401', 'message' => 'Старый пароль введен неверно']];
        }
    }
}
