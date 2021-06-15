<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Controllers\RequesterController;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Nomenclatures;
use App\Models\Suppliers;
use App\Models\Files;
use App\Imports\NomenclaturesImport;
use Carbon\Carbon;

class NomenclatureTableController extends Controller
{
    public function index()
    {   
        $suppliers = Suppliers::all();
        return view('Admin.pages.nomenclatures.index', ['suppliers' => $suppliers]);
    }

    public function uploadFile(Request $request) {
        $validateRules = [
            'article' => ['required', 'numeric', 'min:1'],
            'title' => ['required', 'numeric', 'min:1'],
            'price' => ['required', 'numeric', 'min:1'],
            'amount' => ['required', 'numeric', 'min:1'],
            'startRow' => ['required', 'numeric', 'min:1'],
            'file' => ['required', 'file', 'mimes:xls,xlsx', 'max:15000'],
        ];
        // Валидация
        $validation = $request->validate($validateRules);
        // Библиотека времени
        $date = new Carbon;
        // Берем данные пользователя
        $user = $request->attributes->get('adminInfo');
        // Парсинг загруженной таблицы
        $rows = Excel::toArray(new NomenclaturesImport, $request->file('file'));

        if ($rows) {
            // Берем файл
            $file = $request->file('file');
            // Переименовываем файл
            $filename = md5(time()).'.'.strtolower($file->getClientOriginalExtension());
            // Настраиваем путь для файла
            $location = public_path('files\suppliers\nomenclature\\'.$date->today()->format('m-Y'));
            // Перемещаем файл
            $file->move($location, $filename);
            // Записываем в базу данных файл, который был успешно перемещен
            $file = new Files;

            $file->path = 'files/suppliers/nomenclature/'.$date->today()->format('m-Y').'/';
            $file->name = $filename;
            $file->type = 1;

            $file->createdBy = $user->id;

            $file->save();

            // Выравниваем массив убираем вложенность
            $rows = array_merge(...$rows);
            // Начинаем цикл который будет добавлять записи в базу данных
            foreach($rows as $index => $row) {
                // Отсекаем информацию идущая до самой таблицы
                if ($index >= $request->startRow - 1) {
                    // Проверяем есть ли текущая запись в базе данных
                    $nomenclature = new Nomenclatures;
                    $nomenclatureInfo = $nomenclature->where('article', $row[$request->article - 1])
                        ->where('supplierID', $request->supplierID)
                        ->where('manufacturer', $row[$request->manufacturer - 1])
                        ->first();

                    if ($row[$request->article - 1] == null || $row[$request->title - 1] == null)
                        continue;
                    if ($nomenclatureInfo) {
                        // Смещение индекса на единицу и занесение данных
                        $nomenclatureInfo->update([
                            'article' => $row[$request->article - 1],
                            'supplierArticle' => $row[$request->supplierArticle - 1] ?? null,
                            'supplierID' => $request->supplierID,
                            'title' => $row[$request->title - 1],
                            'additionText' => $row[$request->additionText - 1] ?? null,
                            'manufacturer' => $row[$request->manufacturer - 1] ?? null,
                            'price' => RequesterController::clearNumber($row[$request->price - 1]),
                            'amount' => RequesterController::clearNumber($row[$request->amount - 1]),
                            'updatedBy' => $user->id,
                        ]);
                    } else {

                        // Смещение индекса на единицу и занесение данных
                        Nomenclatures::insertGetId([
                            'article' => $row[$request->article - 1],
                            'supplierArticle' => $row[$request->supplierArticle - 1] ?? null,
                            'supplierID' => $request->supplierID,
                            'title' => $row[$request->title - 1],
                            'additionText' => $row[$request->additionText - 1] ?? null,
                            'manufacturer' => $row[$request->manufacturer - 1] ?? null,
                            'price' => RequesterController::clearNumber($row[$request->price - 1]),
                            'amount' => RequesterController::clearNumber($row[$request->amount - 1]),
                            'updatedBy' => $user->id,
                            'createdBy' => $user->id,
                        ]);
                    }
                    
                }
            }

            return ['ok' => true, 'result' => []];

        } 

        return ['ok' => false, 'result' => []];
    }
    
    public function getNomenclatures(Request $request) {
        switch ($request->order[0]['column']) {
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

        $nomenclatures = Nomenclatures::orWhere('article', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('supplierArticle', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('supplierID', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('nomenclatures.title', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('price', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('amount', 'LIKE', '%'.$request->search['value'].'%')
            ->orWhere('suppliers.title', 'LIKE', '%'.$request->search['value'].'%')
            
            ->join('suppliers', 'nomenclatures.supplierID', '=', 'suppliers.id')
            ->select('nomenclatures.*', 'suppliers.title as supplierName')
            ->offset($request->start)
            ->limit($request->length)
            ->orderBy($columnName, $request->order[0]['dir'])
            ->get();

        $nomenclaturesTotalRows = Nomenclatures::join('suppliers', 'nomenclatures.supplierID', '=', 'suppliers.id')
            ->select('nomenclatures.*', 'suppliers.*')
            ->count();

        return [
            'data' => $nomenclatures,
            'start' => intval($request->start), 
            'length' => intval($request->length),
            'end' => $nomenclaturesTotalRows,
            'recordsTotal' => $nomenclaturesTotalRows,
            'recordsFiltered' => $nomenclaturesTotalRows, 
            'orderBy' => intval($request->order[0]['column'])
        ];
    }

}
