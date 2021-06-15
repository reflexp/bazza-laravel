<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;
use App\Models\Clients;
use Illuminate\Http\Request;
use App\Models\Nomenclatures;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class NomenclatureController extends Controller
{
    public function index() {
        return view('Client.pages.nomenclature');
    }

    public function getNomenclatures(Request $request) {

        $clientCookie = Cookie::get(config('constants.clientCookieName'));

        $clientInfo = Clients::where('sessionHash', $clientCookie ?? 'NaN')->first();

        if($clientInfo) {
            $buyerType = $clientInfo['buyerType'];
        }
        else
        {
            $buyerType = 1;
        }

        if($request->methodType == 'URL') {
            $sortField = $request->sortField;
            $sortType = $request->sortType;
        }
        else
        {
            $sortField = $request->order[0]['column'];
            $sortType = $request->order[0]['dir'];
        }
        switch ($sortField) {
            case '0':
                $columnName = $request->columns[0]['data'];
                break;

            case '1':
                $columnName = $request->columns[1]['data'];
                break;

            case '2':
                $columnName = $request->columns[2]['data'];
                break;

            case '3':
                $columnName = $request->columns[3]['data'];
                break;

            case '4':
                $columnName = $request->columns[4]['data'];
                break;
            default:
                $columnName = 'article';
                break;
        }

        // Создание экземпляра модели
        $nomenclatures = new Nomenclatures;

        // Создание Подсчёт общего коичества строк, но зачем?
        $nomenclaturesAll = $nomenclatures->count();

        // TODO: Добавь комментарии

		$columnName = $request->columns[intVal($request->sortField)]['data'];

        if($request->search == '1' && $request->isQueryKeysCorrect == null && $request->isQueryValuesCorrect == null) {
            
            $nomenclatures = Nomenclatures::where('title', 'LIKE', '%'.$request->name.'%')
                ->where('article', 'LIKE', '%'.$request->article.'%')
                ->offset($request->start)
                ->limit($request->length)
                ->orderBy($columnName, $request->sortType)
                ->get();

            $nomenclaturesAllCount = Nomenclatures::all()->count();

        } else {

            $nomenclatures = Nomenclatures::offset($request->start)
            ->limit($request->length)
            ->orderBy($request->columns[0]['data'], $request->order[0]['dir'])
            ->get();

            $nomenclaturesAllCount = Nomenclatures::all()->count();

        }
        // Цикл изменения цены в зависимости от клиента
        $dataset = [];
        foreach ($nomenclatures as $product)
        {
            // Функция получения цены с учётом наценки от типа клиента и условий поставщика
            $newPrice = RequesterController::getSupplierIncreasedPrice($product['price'], $product['supplierID'], $buyerType);
            $product['price'] = $newPrice['price'];
            array_push($dataset, $product);
        }

        return [
            'data' => $dataset,
            'start' => intval($request->start),
            'length' => intval($request->length),
            'end' => $nomenclaturesAll,
            'recordsTotal' => $nomenclaturesAll,  //$nomenclatures->count()
            'recordsFiltered' => $nomenclaturesAll, // $nomenclatures->count()
            'orderBy' => intval($sortField)
        ];
    }
}
