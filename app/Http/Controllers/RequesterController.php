<?php

namespace App\Http\Controllers;

use App\Models\Suppliers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequesterController extends Controller
{
    public function getToken() {
        return csrf_token();
    }

    public static function clearPhone(string $phone = null)
    {
        if ($phone === null)
        {
            return '';
        }
        $onlyDigits = preg_replace('/\D/', '', $phone);

        if ($onlyDigits[0] === '8') {
            $onlyDigits = substr($onlyDigits, 1);
            $onlyDigits = "7$onlyDigits";
        }

        return "+$onlyDigits";
    }

    public static function clearNumber(string $number)
    {
        $onlyDigits = preg_replace('/\D/', '', $number);
        return "$onlyDigits";
    }

    /**
     * Функция просчёта наценки за товар
     * https://coderun.ru/prostye-otvety/kak-najjti-diapazon-chisel-v-kotorom-nakhoditsya-chislo-v-php/
     * @param int $price            - цена за товар
     * @param int $supplierID   - ID поставщика
     * @param int $buyerType        - default 1 - тип покупателя 1 - Розничный, 2 - Оптовый
     * @return array[float, int] $result - ответ в виде массива, цена с наценкой и процент наценки
     * */

    public static function getSupplierIncreasedPrice( $price = 0, $supplierID = null, $buyerType = 1)
    {
        $supplierInfo = Suppliers::where('id', $supplierID ?? 'NaN')->first();

        if ($supplierInfo === null)
        {
            return $price;
        }
        if ($price === 0)
        {
            return $price;
        }

        /* Тип наценки 0 - обычная наценка, 1 - дифф наценка */
        if ($supplierInfo['increaseType'] === 1)
        {
            // Поиск в каком диапазоне лежит цена
            $rangeIndex =  RequesterController::getPriceRangeIndex($price);

            /* Тип покупателя 1 - розничный, 2 - оптовый */
            if($buyerType === 2)
            {
                // Оптовый
                $buyerTax = $supplierInfo['diffIncreaseWhosale_' . $rangeIndex];
            }
            else
            {
                // Розничный
                $buyerTax = $supplierInfo['diffIncreaseRetail_' . $rangeIndex];
            }
        }
        else
        {
            /* Тип покупателя 1 - розничный, 2 - оптовый */
            if($buyerType === 2)
            {
                // Оптовый
                $buyerTax = $supplierInfo['taxWhosale'];
            }
            else
            {
                // Розничный
                $buyerTax = $supplierInfo['taxRetail'];
            }
        }

        $increaseStorage = $supplierInfo['increaseStorage'];
        $increaseDelivery = $supplierInfo['increaseDelivery'];
        $increaseIncome = $supplierInfo['increaseIncome'];
        $commonTax = $supplierInfo['commonTax'];



        // Формула: ЦенаЗаТовар = НаценкаДоставка + НаценкаХранение + НаценкаЗаработок + НаценкаНалогОбщий + НаценкаПокупателя
        $totalPercent = $increaseStorage + $increaseDelivery + $increaseIncome + $commonTax + $buyerTax;

        // Если формат ответа - процент, то выводтся процент на этот товар

        // Добавление процента к цене за товар, округление до 2х знаков
        $totalPrice = round($price + $price * ($totalPercent / 100), 2);

        return ['price' => $totalPrice, 'percent' => $totalPercent];
    }
    private static function getPriceRangeIndex($price)
    {
        /**
        'supplierIncreaseGradationRange' =>
            [
                1 => [0, 499],              // diffIncrease_1
                2 => [500, 1999],           // diffIncrease_2
                3 => [2000, 9999],          // diffIncrease_3
                4 => [10000, 59999],        // diffIncrease_4
                5 => [60000, 99999999999],  // diffIncrease_5
            ]
        */
        $increaseGradationRange = config('prices.supplierIncreaseGradationRange');

        // Цикл по каждому индексу массива диапазонов цен.
        foreach ($increaseGradationRange as $key => $range)
        {
            // Функция проверки принадлежания цены к Диапазону цен.
            // Костыль с intval(), т.к функция считает только int числа. Погрешность в вычислениях в 1 тенге.
            $check_range = filter_var(intval($price), FILTER_VALIDATE_INT, array(
                'default' => false,
                'options' => [
                    'min_range' => $range[0],
                    'max_range' => $range[1],
                ],
            ));
            // Если в этом диапазоне есть число, то выводим ключ массива
            if ($check_range !== false) {
                return $key;
            }
        }
        // Если цикл ничего не вернул, то выводим по умолчанию индекс = 1
        return 1;
    }

    public static function sendSMSMessage(string $phone, string $message) 
    {   
        if (!empty($phone) && !empty($message)) {
            $dataset = [
                'login' => 'menumarket',
                'psw' => 'uTM1e45yex',
                'phones' => $phone,
                'mes' => $message,
                'fmt' => 3,
            ];
    
            $response = Http::asForm()->post('https://smsc.kz/sys/send.php', $dataset);
    
            if ($response->successful()) {
                return ['ok' => true, 'result' => ['sendMessageResult' => $response->body()]];
            } else {
                return ['ok' => false, 'result' => ['message' => 'Ошибка отправки сообщения']];
            }
        }
    }

    public static function ddd($array)
    {
        dd(json_decode(json_encode($array), true));
    }
}
