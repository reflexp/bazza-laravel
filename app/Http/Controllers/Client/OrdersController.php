<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use App\Models\Clients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

use App\Models\Shopcart;
use App\Models\Shopcartproducts;
use App\Models\Storages;
use App\Models\Orders;
use App\Models\Orderproducts;

class OrdersController extends Controller
{
    public function indexPage() {

        $clientCookie = Cookie::get(config('constants.clientCookieName'));
        $cartHash = Cookie::get(config('constants.clientCookieCartName'));

        $clientInfo = Clients::where('sessionHash', $clientCookie ?? 'NaN')->first();

        if($clientInfo) {
            $buyerType = $clientInfo['buyerType'];
        }
        else
        {
            $buyerType = 1;
        }

        $storages = Storages::where('active', 1)->where('deleted', 0)->get();

        // Извлекаем данные корзины из shopcart, shopcartproducts и nomenclature
        $products = Shopcart::where('cartHash', $cartHash)
            ->join('shopcartproducts', 'shopcart.id', '=', 'shopcartproducts.cartID')
            ->join('nomenclatures', 'shopcartproducts.productID', '=', 'nomenclatures.id')
            ->select('shopcart.cartHash', 'nomenclatures.*', 'shopcartproducts.*')
            ->get();


        // Цикл изменения цены в зависимости от клиента
        $dataset = [];
        foreach ($products as $product)
        {
            // Функция получения цены с учётом наценки от типа клиента и условий поставщика
            $newPrice = RequesterController::getSupplierIncreasedPrice($product['price'], $product['supplierID'], $buyerType);
            $product['price'] = $newPrice['price'];
            array_push($dataset, $product);
        }


        return view('Client.pages.account.neworder', ['storages' => $storages, 'products' => $products]);
    }

    public function addOrder(Request $request) {
        $validateRules = [
            'storage' => ['required', 'numeric'],
            'deliveryType' => ['required', 'numeric'],
            'street' => ['required_if:deliveryType, 2', 'max:128'],
            'build' => ['required_if:deliveryType, 2', 'max:10'],
            'flat' => ['required_if:deliveryType, 2', 'max:10'],

            'comment' => ['max:1024'],
            'shopRules' => 'accepted'
        ];

        $validation = $request->validate($validateRules);

        $cartCookieName = config('constants.clientCookieCartName');
        $cartHash = Cookie::get($cartCookieName);
        $totalSum = 0;
        $totalAmount = 0;

        $clientInfo = $request->attributes->get('clientInfo');

        // Извлекаем данные корзины из shopcart, shopcartproducts и nomenclature
        $products = Shopcart::where('cartHash', $cartHash)
            ->join('shopcartproducts', 'shopcart.id', '=', 'shopcartproducts.cartID')
            ->join('nomenclatures', function ($join) {
                $join->on('shopcartproducts.productID', '=', 'nomenclatures.id')
                    ->where('nomenclatures.active', 1)
                    ->where('nomenclatures.deleted', 0);
            })
            ->select('shopcart.cartHash', 'nomenclatures.*', 'shopcartproducts.*','nomenclatures.amount as nomAmount' )
            ->get();

        $productsNewPrice = [];
        foreach($products as $product) {
            $clientPrice = RequesterController::getSupplierIncreasedPrice($product->price, $product->supplierID ,$clientInfo['buyerType']);
            $totalSum += $clientPrice['price'] * $product->amount;

            $product['price'] = $clientPrice['price'];

            array_push($productsNewPrice, $product);
        }

        if ($request->deliveryType === 2)
        {
            $fullAddress = 'ул. ' . $request->street . ', д. ' . $request->build . ', кв. ' . $request->flat;
        }
        else { $fullAddress = null; }

        /* Если клиент - оптовый, то по умолчанию ожидается предоплата*/
        if ($clientInfo['buyerType'] === 2)
        {
            $prePayment = true;
        }
        else
        {
            $prePayment = false;
        }

        $orderID = Orders::insertGetId([
            'clientID' => $clientInfo['id'],
            'totalSum' => $totalSum,
            'comment' => $request->comment,
            'needPrePayment' => $prePayment,
            'deliveryType' => $request->deliveryType,
            'deliveryToStorageID' => $request->storage,
            'deliveryAddressInCity' => $fullAddress,
            'status' => 1,
        ]);

        $shopcartproducts = new Shopcartproducts;

        foreach($productsNewPrice as $product) {
            Orderproducts::insertGetId([
                'nomenclatureID' => $product->productID,
                'price' => $product->price,
                'amount' => $product->amount,
                'totalPrice' => $product->price * $product->amount,
                'orderID' => $orderID,
            ]);

            Shopcartproducts::where('id', $product->id)->delete();
        }
        Shopcart::where('cartHash', $cartHash)->delete();

        // Очистка куки пользователя
        Cookie::queue(Cookie::forget($cartCookieName));

        return ['ok' => true, 'result' => []];
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
//                    'suppliers.id as supplierID',
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
}
