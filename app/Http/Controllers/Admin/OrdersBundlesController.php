<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;
use App\Models\Storageusers;
use Illuminate\Http\Request;

use App\Models\Orders;
use App\Models\Storages;
use App\Models\Orderproducts;
use App\Models\OrdersBundles;
use App\Models\OrdersBundlesItems;

class OrdersBundlesController extends Controller
{
    public function indexPage() {
        return view('Admin.pages.orders.bundles');
    }

    public function orderbundleInfoPage($id) {

        $ordersbundleInfo = $this->getOrdersFromBundle($id);
//        RequesterController::ddd($ordersbundleInfo);
        return view('Admin.pages.orders.bundle_info', compact('ordersbundleInfo'));
    }

    public function getOrdersBundles(Request $request) {

        /* Информация о пользователе */
        $adminInfo = $request->attributes->get('adminInfo');
        $adminStorages = Storageusers::where('userID', $adminInfo['id'])->pluck('storageID')->toArray();
        $ordersBundles = new OrdersBundles;

        /* Список всех статусов */
        $statuses = config('statuscodes');

        // Если роль 2 - менеджер склада, 4 кладовщик, 5 - продавец, то делать разделение по складам
        if (in_array($adminInfo['roleID'], [2, 4, 5]))
        {
            $ordersBundles = $ordersBundles->whereIn('storageID', $adminStorages);
        }

        $ordersBundlesInfo = $ordersBundles
            ->join('storages', 'orders_bundles.storageID', 'storages.id' )
            ->select('orders_bundles.*', 'storages.title as storageTitle')
            ->orderBy('id', 'DESC')
            ->get();

        $bundlesArray = [];
        foreach ($ordersBundlesInfo as $ordersBundle)
        {
            /* Дополнение информации о статусе */
            $bStatus = $ordersBundle->status;
            $ordersBundle['status'] = [
                'id' => $bStatus,
                'color' => $statuses['bundle'][$bStatus]['color'],
                'text' => $statuses['bundle'][$bStatus]['text'],
            ];

            /* Подсчёт количества заказов */
            $ordersBundle['ordersCount'] = OrdersBundlesItems::where('bundleID', $ordersBundle['id'])->count();

            array_push($bundlesArray, $ordersBundle);
        }
        return ['ok' => true, 'result' => ['ordersbundles' => $bundlesArray]];
    }

    public function getOrdersFromBundle($id) {
        $ordersbundleInfo = OrdersBundles::where('id', $id ?? 'NaN')->first();
        $statuses = config('statuscodes');

        if (!$ordersbundleInfo)
        {
            return abort(404);
        }

        $bStatus = $ordersbundleInfo->status;
        $ordersbundleInfo['status'] = [
            'id' => $bStatus,
            'color' => $statuses['bundle'][$bStatus]['color'],
            'text' => $statuses['bundle'][$bStatus]['text'],
        ];

        $orders = OrdersBundlesItems::where('bundleID', $ordersbundleInfo['id'])
            ->join('orders', 'orders.id', 'orders_bundles_items.orderID')
            ->join('clients', 'orders.clientID', 'clients.id')
            ->select(
                'orders.*',
                'orders_bundles_items.*',
                'clients.id as clientID',
                'clients.name as clientName',
                'clients.login as clientLogin',
                'clients.email as clientEmail',
                'clients.buyerType as buyerType'
            )
            ->orderBy('orderID', 'ASC')
            ->get();


        $ordersArr = [];
        foreach ($orders as $order) {
            // Обновление статуса в зависимости от id
            $order['status'] = [
                'id' => $order->status,
                'color' => $statuses['order'][$order->status]['color'],
                'text' => $statuses['order'][$order->status]['text'],
            ];

            $order->allStatuses = $statuses;

            // Обновление информации о складе
            $storage = Storages::where('id', $order->deliveryToStorageID)->first();
            $order->storage = $storage;

            // Обновление информации о товаров в заказе
            $products = Orderproducts::where('orderID', $order->orderID)
                ->join('nomenclatures', 'nomenclatures.id', 'orderproducts.nomenclatureID')
                ->join('suppliers', 'nomenclatures.supplierID', 'suppliers.id' )
                ->select(
                    'nomenclatures.*',
                    'orderproducts.*',
                    'suppliers.title as supplierTitle'
                )
                ->get();

            $productsArr = [];

            foreach ($products as $product) {
                $product['status'] = [
                    'id' => $product->status,
                    'color' => $statuses['product'][$product->status]['color'],
                    'text' => $statuses['product'][$product->status]['text'],
                ];
                array_push($productsArr, $product);
            }

            $order['products'] = $productsArr;
            array_push($ordersArr, $order);
        }

        $ordersbundleInfo['orders'] = $ordersArr;

        return $ordersbundleInfo;
    }

    public function createOrdersBundle(Request $request) {
        $orders = json_decode($request->orders, true);
        $adminInfo = $request->attributes->get('adminInfo');

        $storageInfoByOrder = Orders::whereIn('id', $orders)->pluck('deliveryToStorageID')->first();
        $ordersBundleID = OrdersBundles::insertGetId([
            'status' => 1,
            'storageID' => $storageInfoByOrder,
            'comment' => $request->comment,
            'createdBy' => $adminInfo['id']
        ]);

        foreach ($orders as $order)
        {
            OrdersBundlesItems::insertGetId([
                'orderID' => $order,
                'bundleID' => $ordersBundleID,
            ]);
        }
        return ['ok' => true, 'result' => []];
    }

    public function getSuppliersFromBundle(Request $request) {
        $suppliers = OrdersBundlesItems::where('bundleID', $request->id)
            ->join('orderproducts', 'orderproducts.orderID', 'orders_bundles_items.orderID')
            ->join('nomenclatures', 'nomenclatures.id', 'orderproducts.nomenclatureID')
            ->join('suppliers', 'suppliers.id', 'nomenclatures.supplierID')
            ->select(
                'orderproducts.*',
                'orders_bundles_items.orderID',
                'nomenclatures.supplierID',
                'nomenclatures.article',
                'nomenclatures.supplierArticle',
                'nomenclatures.amount as nomenclatureAmount',
                'nomenclatures.price',
                'nomenclatures.title',
                'suppliers.id',
                'suppliers.title',
                'suppliers.storageID',
            )
            ->get();

        foreach ($suppliers as $supplier) {
            $supplier->orders = OrdersBundlesItems::where('bundleID', $request->id)->get();
        }

        return $suppliers;
    }


    public function removeOrderFromBundle(Request $request) {
        $validateRules = [
            'bundleID' => ['required'],
            'orderID' => ['required'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $orderProd = OrdersBundlesItems::where('bundleID', $request->bundleID)->where('orderID', $request->orderID);
        $orderProd->delete();

        return ['ok' => true, 'result' => []];
    }

}
