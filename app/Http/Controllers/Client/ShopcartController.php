<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

use App\Http\Controllers\RequesterController;
use App\Models\Clients;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

use App\Models\Shopcart;
use App\Models\Shopcartproducts;
use App\Models\Nomenclatures;

class ShopcartController extends Controller
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

        return view('Client.pages.shopcart', ['products' => $dataset]);
    }

    public function addToCart(Request $request) {
        $validateRules = [
            'amount' => ['required', 'numeric', 'min:1', 'max:100000']
        ];

        $validation = $request->validate($validateRules);
        // Получение времени жизни Auth куки для Админа
        $cartCookieSessionKeepAlive = config('constants.clientCookieCartKeepAlive');
        // Берем имя для куки из конфига
        $cartCookieName = config('constants.clientCookieCartName');
        // Инициализируем новую модель
        $shopcart = new Shopcart;
        // Проверяем существует ли куки, если не существует создаем куки, в другом случае берем значение куки
        if (Cookie::get($cartCookieName) == null) {
            $cartHash = Hash::make(time() . 'cart246');
            // Добавление в таблицу Shopcart новый hash
            $shopcart->cartHash = $cartHash;
            $shopcart->save();
            // Установка куки файла пользователю
            Cookie::queue($cartCookieName, $cartHash, $cartCookieSessionKeepAlive);
        } else {
            $cartHash = Cookie::get($cartCookieName);
        }
        // Извлекаем данные корзины из shopcart
        $shopcart = Shopcart::where('cartHash', $cartHash)->first();
        // Проверяем существует ли куки у пользователя в базе данных в случае удаления из базы
        if (!$shopcart) {
            $shopcart = new Shopcart;
            // Добавление в таблицу Shopcart новый hash
            $shopcart->cartHash = $cartHash;
            $shopcart->save();
        }
        // Ищем совпадения добавляемого товара с данными в таблице shopcartproducts
        $shopcartProduct = Shopcartproducts::where('productID', $request->id)
            ->where('CartID', $shopcart->id)
            ->first();

        // Если есть совпадения, то обновляет количество, в другом случае добавляет данные в таблицу
        if($shopcartProduct) {
            $shopcartProduct->amount = abs($shopcartProduct->amount) + abs($request->amount);
            $shopcartProduct->update();
        } else {
            $shopcartProducts = new Shopcartproducts;
            // Добавляем параметры с request
            $shopcartProducts->cartID = $shopcart->id;
            $shopcartProducts->productID = $request->id;
            $shopcartProducts->amount = abs($request->amount);

            $shopcartProducts->save();
        }
        
        return ['ok' => true, 'result' => ['cookie' => $cartHash]];
    }

    public function getCartCount(Request $request) {
        $cartCookieName = config('constants.clientCookieCartName');

        if (Cookie::get($cartCookieName) != null) {
            $cookie = Cookie::get($cartCookieName);
            // Извлекаем данные корзины из shopcart
            $shopcart = Shopcart::where('cartHash', $cookie)->first();
            if ($shopcart) {
                // Считаем количество элементов
                $shopcartproducts = Shopcartproducts::where('CartID', $shopcart->id)->sum('amount');
                return $shopcartproducts;
            }
        }
    }

    public function editCartAmount(Request $request) {
        $clientCookie = Cookie::get(config('constants.clientCookieName'));
        $cartHash = Cookie::get(config('constants.clientCookieCartName'));
        
        // Находим корзину по хэшу
        $productInfo = Shopcartproducts::where('productID', $request->productID)
            ->join('shopcart', 'shopcart.id', 'shopcartproducts.cartID')
            ->where('shopcart.cartHash', $cartHash)
            ->join('nomenclatures', 'nomenclatures.id', 'shopcartproducts.productID')
            ->select(
                'nomenclatures.price', 
                'nomenclatures.amount as nomenclatureAmount', 
                'shopcartproducts.*',
                'shopcart.cartHash',
            )
            ->first();
        
        // Обновляем количество
        $productInfo->amount = $request->amount;
        // Сохраняем обновленное количество
        $productInfo->update();

        // Ищем информацию о типе клиента
        $clientInfo = Clients::where('sessionHash', $clientCookie ?? 'NaN')->first();
        // Если данные нашлись приравниваем buyerType, если нет то 1
        $buyerType = ($clientInfo) ? $clientInfo['buyerType'] : 1;
        
        // Находим все товары в корзине для пересчета суммы
        $products = Shopcart::where('cartHash', $cartHash)
            ->join('shopcartproducts', 'shopcartproducts.cartID', '=', 'shopcart.id')
            ->join('nomenclatures', 'shopcartproducts.productID', '=', 'nomenclatures.id')
            ->select(
                'nomenclatures.price', 
                'nomenclatures.supplierID', 
                'shopcartproducts.amount', 
                'shopcart.cartHash'
            )
            ->get();

        // Цикл пересчета цены
        $fullPrice = 0;
        foreach ($products as $product) {
            $increasedPrice = RequesterController::getSupplierIncreasedPrice($product['price'], $product['supplierID'], $buyerType);
            $fullPrice += $increasedPrice['price'] * $product->amount;
        }

        $productInfo->fullPrice = $fullPrice;

        return $productInfo;
    }

    public function removeFromCart(Request $request) {
        $clientCookie = Cookie::get(config('constants.clientCookieName'));
        $cartHash = Cookie::get(config('constants.clientCookieCartName'));

        // Ищем позицию в корзину для удаления
        $productInfo = Shopcartproducts::where('productID', $request->productID)
            ->join('shopcart', 'shopcart.id', 'shopcartproducts.cartID')
            ->where('shopcart.cartHash', $cartHash)
            ->delete();

        // Находим все товары в корзине для пересчета суммы
        $products = Shopcart::where('cartHash', $cartHash)
            ->join('shopcartproducts', 'shopcartproducts.cartID', '=', 'shopcart.id')
            ->join('nomenclatures', 'shopcartproducts.productID', '=', 'nomenclatures.id')
            ->select(
                'nomenclatures.price', 
                'nomenclatures.supplierID', 
                'shopcartproducts.amount', 
                'shopcart.cartHash'
            )
            ->get();

        // Ищем информацию о типе клиента
        $clientInfo = Clients::where('sessionHash', $clientCookie ?? 'NaN')->first();
        // Если данные нашлись приравниваем buyerType, если нет то 1
        $buyerType = ($clientInfo) ? $clientInfo['buyerType'] : 1;

        /// Цикл пересчета цены
        $fullPrice = 0;
        foreach ($products as $product) {
            $increasedPrice = RequesterController::getSupplierIncreasedPrice($product['price'], $product['supplierID'], $buyerType);
            $fullPrice += $increasedPrice['price'] * $product->amount;
        }

        return ['ok' => true, 'result' => ['fullprice' => $fullPrice]];
    }
}
