<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

use App\Http\Controllers\RequesterController;
use App\Models\Nomenclatures;
use App\Models\OrdersBundlesItems;
use App\Models\Storageusers;
use App\Rules\PhoneValidation;
use http\Client;
use Illuminate\Http\Request;

use App\Models\Users;
use App\Models\Orders;
use App\Models\Clients;
use App\Models\Storages;
use App\Models\Orderproducts;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function indexPage() {
        return view('Admin.pages.orders.index');
    }

    public function getOrders(Request $request) {
        /* Вывод закзазов*/
        $adminInfo = $request->attributes->get('adminInfo');
        $adminStorages = Storageusers::where('userID', $adminInfo['id'])->pluck('storageID')->toArray();
        $orders = new Orders;
        $getOrderStatuses = config('statuscodes');
        $ordersArray = [];

        // Если роль 2 - менеджер склада, 4 кладовщик, 5 - продавец, то делать разделение по складам
        if (in_array($adminInfo['roleID'], [2, 4, 5]))
        {
            $orders = $orders->whereIn('deliveryToStorageID', $adminStorages);
        }

        /* Сортировка по столбцам (по принимаемым данным от datatables) */
        $columnNumber = $request->order[0]['column'];
        $columnName = $request->columns[$columnNumber]['name'];

        $ordersTotalRows = $orders->count();

        $ordersInfo = $orders->offset($request->start)
            ->limit($request->length)
            ->orderBy($columnName, $request->order[0]['dir'])
            ->get();

        /* Цикл прохождения по каждому заказу */
        foreach ($ordersInfo as $order)
        {
            /* Получение всех продуктов в заказе
             * Join Номенклатура для вывода текста, а не ID
             * Join Поставщики для вывода текста, а не ID
             * */
            $orderProducts = Orderproducts::where('orderID', $order['id'])
                ->join('nomenclatures', 'orderproducts.nomenclatureID', 'nomenclatures.id' )
                ->join('suppliers', 'nomenclatures.supplierID', 'suppliers.id' )
                ->select(
                    'orderproducts.*',
                    'nomenclatures.price as nomenclaturePrice',
                    'nomenclatures.amount as nomenclatureAmount',
                    'nomenclatures.title as title',
                    'nomenclatures.article as article',
                    'nomenclatures.manufacturer as manufacturer',
                    'nomenclatures.additionText as additionText',
//                    'suppliers.id as supplierID',
                    'suppliers.title as supplierTitle'
                )
                ->get();

            /* Получение информации о нахождении в пакете */
            $inBundleItem = OrdersBundlesItems::where('orderID', $order['id'])->exists() ;

            /* Получение информации о складе */
            $storageInfo = Storages::where('id', $order['deliveryToStorageID'])->first();

            /* Получение информации о клиенте */
            $clientInfoProducts = Clients::where('id', $order['clientID'])
                ->exclude(['password', 'sessionHash', 'updated_at', 'created_at'])
                ->first();


            /* Цикл прохождения по каждой позиции в заказе */
            $productsArray = [];
            foreach ($orderProducts as $orderProduct)
            {
                /* Замена statusID у продукта на массив значений из конфигурации */
                $ps = $orderProduct['status'];
                $orderProduct['status'] = [
                    'id' => $ps,
                    'color' => $getOrderStatuses['product'][$ps]['color'],
                    'text' => $getOrderStatuses['product'][$ps]['text'],
                ];
                array_push($productsArray, $orderProduct);
            }

            /* Замена statusID у заказа на массив значений из конфигурации */
            $os = $order['status'];
            $order['status'] = [
                'id' => $os,
                'color' => $getOrderStatuses['order'][$os]['color'],
                'text' => $getOrderStatuses['order'][$os]['text'],
            ];

            $order['inBundle'] = $inBundleItem;
            $order['products'] = $productsArray;
            $order['client'] = $clientInfoProducts;
            $order['storage'] = $storageInfo;

            array_push($ordersArray, $order);
        }

        return [
            'data' => $ordersArray,
            'start' => intval($request->start),
            'length' => intval($request->length),
            'end' => $ordersTotalRows,
            'recordsTotal' => $ordersTotalRows,
            'recordsFiltered' => count($ordersArray),
            'orderBy' => intval($request->order[0]['column'])
        ];
    }

    public function editOrder(Request $request) {
        $validateRules = [
            'orderID' => ['required', 'exists:orders,id'],
            'statusPay' => ['numeric'],
            'status' => ['required', 'numeric'],
            'comment' => ['max:2048'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $order = Orders::where('id', $request->orderID ?? 'NaN');
        $orderInfo = $order->first();

        /* Если на обновление пришло только поле status OrderBundles*/
        $order->update([
            'paymentConfirmed' => $request->statusPay ?? $orderInfo['paymentConfirmed'],
            'status' => $request->status,
            'comment' => $request->comment ?? $orderInfo['comment'],
            ]);

        return ['ok' => true, 'result' => []];
    }
    public function editOrderProduct(Request $request) {
        $validateRules = [
            'productID' => ['required', 'exists:orderproducts,id'],
            'amount' => ['required', 'numeric'],
            'status' => ['required', 'numeric'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $orderProd = Orderproducts::where('id', $request->productID);
        $orderProdInfo = $orderProd->first();
        $orderProd->update([
            'amount' => $request->amount,
            'status' => $request->status,
        ]);
        $this->reCountTotalOrder($orderProdInfo['orderID']);

        return ['ok' => true, 'result' => []];
    }
    public function addOrderProduct(Request $request) {
        $validateRules = [
            'orderID' => ['required', 'numeric', 'exists:orders,id'],
            'newArticle' => ['required', 'numeric', 'exists:nomenclatures,id'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Информация о заказе
        $orderInfo = Orders::where('id', $request->orderID)->first();
        // Информация о клиенте
        $clientInfo = Clients::where('id', $orderInfo['clientID'])->first();

        $nomenclatureInfo = Nomenclatures::where('id', $request->newArticle)->first();

        $clientPrice = RequesterController::getSupplierIncreasedPrice($nomenclatureInfo['price'], $nomenclatureInfo['supplierID'], $clientInfo['buyerType']);

        $orderProdInfo = Orderproducts::insertGetId([
            'nomenclatureID' => $nomenclatureInfo['id'],
            'price' => $clientPrice['price'],
            'amount' => 1,
            'totalPrice' => $clientPrice['price'],
            'orderID' => $request->orderID,
        ]);

        $this->reCountTotalOrder($request->orderID);

        return ['ok' => true, 'result' => []];
    }

    public function changeOrderProduct(Request $request) {
        $validateRules = [
            'orderID' => ['required', 'numeric', 'exists:orders,id'],
            'newArticle' => ['required', 'numeric', 'exists:nomenclatures,id'],
            'oldProductID' => ['required', 'numeric', 'exists:nomenclatures,id'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        // Информация о заказе
        $orderInfo = Orders::where('id', $request->orderID)->first();
        // Информация о клиенте
        $clientInfo = Clients::where('id', $orderInfo['clientID'])->first();

        $orderProdInfo = Orderproducts::where('id', $request->oldProductID)->delete();

        $nomenclatureInfo = Nomenclatures::where('id', $request->newArticle)->first();

        $clientPrice = RequesterController::getSupplierIncreasedPrice($nomenclatureInfo['price'], $nomenclatureInfo['supplierID'], $clientInfo['buyerType']);

        $orderProdInfo = Orderproducts::insertGetId([
            'nomenclatureID' => $nomenclatureInfo['id'],
            'price' => $clientPrice['price'],
            'amount' => 1,
            'totalPrice' => $clientPrice['price'],
            'orderID' => $request->orderID,
        ]);

        $this->reCountTotalOrder($request->orderID);

        return ['ok' => true, 'result' => []];
    }
    public function removeOrderProduct(Request $request) {
        $validateRules = [
            'productID' => ['required', 'exists:orderproducts,id'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $orderProd = Orderproducts::where('id', $request->productID);
        $orderProdInfo = $orderProd->first();
        $orderProd->delete();

        $this->reCountTotalOrder($orderProdInfo['orderID']);

        return ['ok' => true, 'result' => []];
    }

    public function findProductsByArticle(Request $request) {
        $validateRules = [
            'article' => ['required'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $products = Nomenclatures::where('article', 'like', '%' . $request->article . '%')->limit(5)->get();

        if ($products->count() > 0)
        {
            return ['ok' => true, 'result' => ['products' => $products]];
        }
        else
        {
            return ['false' => true, 'result' => []];
        }

    }

    private function reCountTotalOrder($orderID) {
        // Получить все товары в данном заказе
        // Взять цену за 1 шт и умножить на количество, записать в total sum
        // Сложить все цены за товары и записать в итоговую стоимость заказа

        $order = Orders::where('id', $orderID);
        $orderInfo = $order->first();
        $orderProducts = Orderproducts::where('orderID', $orderInfo['id']);
        $orderProductsInfo = $orderProducts->get();
        $totalOrderSum = 0;

        foreach ($orderProductsInfo as $product)
        {
            /* Итоговая цена за всё количеество товара */
            $totalProductPrice = $product['price'] * $product['amount'];

            /* Итоговая цена за всё товары в заказе */
            $totalOrderSum += $totalProductPrice;

            /* Пересчёт идёт без запроса на новую цену, т.е по текущей на момент оформления заказа.*/
            $orderProducts->where('id', $product['id'])->update([
                'totalPrice' => $totalProductPrice
            ]);
        }

        /* Обновление заказа: итоговая сумма, не оплачен, и статус - на подтверждение клиенту*/
        $order->update([
            'totalSum' => $totalOrderSum,
            'paymentConfirmed' => 0,
            'userPaymentSum' => 0,
//            'status' => 5,
        ]);
    }


    /* TEST functions START ====================== */
    public function getOrders2(Request $request) {
        /* Вывод закзазов*/
        $adminInfo = $request->attributes->get('adminInfo');
        $adminStorages = Storageusers::where('userID', $adminInfo['id'])->pluck('storageID')->toArray();
        $orders = new Orders;
        $getOrderStatuses = config('statuscodes');
        $ordersArray = [];

        // Если роль 2 - менеджер склада, 4 кладовщик, 5 - продавец, то делать разделение по складам
        if (in_array($adminInfo['roleID'], [2, 4, 5]))
        {
            $orders = $orders->whereIn('deliveryToStorageID', $adminStorages);
        }

//        /* Сортировка по столбцам (по принимаемым данным от datatables) */
//        $columnNumber = $request->order[0]['column'];
//        $columnName = $request->columns[$columnNumber]['name'];


        $ordersTotalRows = $orders->count();

        $ordersInfo = $orders
//            ->limit($request->length)
//            ->orderBy($columnName, $request->order[0]['dir'])
            ->get();

        foreach ($ordersInfo as $order)
        {
            $orderProducts = Orderproducts::where('orderID', $order['id'])
                ->join('nomenclatures', 'orderproducts.nomenclatureID', 'nomenclatures.id' )
                ->join('suppliers', 'nomenclatures.supplierID', 'suppliers.id' )
                ->select(
                    'orderproducts.*',
                    'nomenclatures.price as nomenclaturePrice',
                    'nomenclatures.amount as nomenclatureAmount',
                    'nomenclatures.title as title',
                    'nomenclatures.article as article',
                    'nomenclatures.manufacturer as manufacturer',
                    'nomenclatures.additionText as additionText',
                    'suppliers.title as supplierTitle'
                )
                ->get();
            $clientInfoProducts = Clients::where('id', $order['clientID'])
                ->exclude(['password', 'sessionHash', 'updated_at', 'created_at'])
                ->first();
            $storageInfo = Storages::where('id', $order['deliveryToStorageID'])->first();

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
            $order['client'] = $clientInfoProducts;
            $order['storage'] = $storageInfo;

            array_push($ordersArray, $order);
        }

        dd(json_decode(json_encode($ordersArray)));
    }

    public function FAKERsetClientsOrdersStorages() {
        $a = microtime(true);

    //        $orders = Orders::all();
    //        $clients = Clients::all();
    //        $orderproducts = Orderproducts::all();
    //        $storages = Storages::all();

        $ordersArray = [];
        for($i = 0; $i < 1000; $i++) // Клиенты
        {
           $clientID =  Clients::insertGetId([
                'name' => md5(time() . 'name'),
                'login' => '+' . time() . rand(1024, 5000),
                'email' => md5(time()),
                'buyerType' => rand(0, 1),
                'password' => md5(time()),
                'sessionHash' => md5(time() . time()),
                'active' => '1',
                'deleted' => '0',
                'updated_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d'),
            ]);

            $storageID =  Storages::insertGetId([
                'title' => md5(time() . 'name'),
                'contactPhone' => '+' . time() . rand(1024, 5000),
                'cityID' => rand(1, 15),
                'address' => md5(time() . time()) . md5(time()),
                'active' => '1',
                'deleted' => '0',
                'updated_at' => date('Y-m-d'),
                'created_at' => date('Y-m-d'),
            ]);

            for($j = 0; $j < 20; $j++) // Клиенты
            {

                $orderID =  Orders::insertGetId([
                    'clientID' => $clientID,
                    'contactPhone' => '+' . time() . rand(0, 1024),
                    'totalSum' => rand(1024, 900000) ,
                    'comment' => md5(time()),
                    'deliveryType' => rand(0, 1),
                    'deliveryToStorageID' => $storageID,
                    'deliveryAddressInCity' => rand(0, 1),
                    'needPrePayment' => rand(0, 1),
                    'paymentConfirmed' => rand(0, 1),
                    'status' => rand(0, 4),
                    'processedBy' => rand(0, 5),
                    'updated_at' => date('Y-m-d'),
                    'created_at' => date('Y-m-d'),
                ]);

                for($k = 0; $k < 15; $k++)
                {
                    $orderProdID =  Orderproducts::insertGetId([
                        'orderID' => $orderID,
                        'price' => rand(1024, 900000),
                        'amount' =>  rand(0, 1024),
                        'totalPrice' => rand(1024, 900000),

                        'updated_at' => date('Y-m-d'),
                        'created_at' => date('Y-m-d'),
                    ]);
                }
            }

        }
        $result = ['time1' => round(microtime(true) - $a, 4)];
        dd($result);

        return ['time1' => round(microtime(true) - $a, 4), 'arr' => $ordersArray];
        }
    /* TEST functions END ======================== */

}
