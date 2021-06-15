<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Suppliers;

class SupplyController extends Controller
{
    public function index()
    {   
        return view('Admin.pages.suppliers.index');
    }

    public function create()
    {
        $storages = Storages::where('active', 1)->where('deleted', 0)->get();
        return view('Admin.pages.suppliers.create', compact('storages'));
    }

    public function edit($id)
    {   
        $storages = Storages::where('active', 1)->where('deleted', 0)->get();
        $supplier = Suppliers::findOrFail($id);
        return view('Admin.pages.suppliers.edit', compact('supplier', 'storages'));
    }

    public function addSupplier(Request $request)
    {  
        $validateRules = [
            'title' => ['required', 'string'],
            // 'commonTax' => ['required', 'numeric'],
            // 'increaseIncome' => ['required', 'numeric'],
            // 'increaseDelivery' => ['required', 'numeric'],
            // 'increaseRetail' => ['required', 'numeric'],
            // 'increaseWholesale' => ['required', 'numeric'],
            // 'increaseBigWholesale' => ['required', 'numeric'],

            'storageID' => ['required', 'numeric'],
            'increaseType' => ['required', 'numeric'],
            'taxType' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
            'yurinfo' => ['required', 'string', 'max:2048'],
        ];

        $validation = $request->validate($validateRules);

        $supplier = new Suppliers;

        $supplier->title = $request->title;
        $supplier->increaseType = $request->increaseType;
        $supplier->taxType = $request->taxType;
        $supplier->storageID = $request->storageID;
        $supplier->exchange1RUB = $request->exchange;
        $supplier->yurinfo = $request->yurinfo;
        
        $supplier->save();

        return ['ok' => true, 'result' => []];
    }

    public function getSupplier(Request $request)
    {
        $supplier = Suppliers::findOrFail($request->id);
        return $supplier;
    }

    public function getSuppliers()
    {
        $suppliers = Suppliers::all();
        return ['ok' => true, 'result' => ['suppliers' => $suppliers]];
    }

    public function editSupplier(Request $request)
    {   
        $validateRules = [
            'title' => ['required', 'string'],
            // 'commonTax' => ['required', 'numeric'],
            // 'increaseIncome' => ['required', 'numeric'],
            // 'increaseDelivery' => ['required', 'numeric'],
            // 'increaseRetail' => ['required', 'numeric'],
            // 'increaseWholesale' => ['required', 'numeric'],
            // 'increaseBigWholesale' => ['required', 'numeric'],
            'increaseType' => ['required', 'numeric'],
            'taxType' => ['required', 'numeric'],
            'exchange' => ['required', 'numeric'],
            'yurinfo' => ['required', 'string', 'max:2048'],
        ];

        $validation = $request->validate($validateRules);

        $supplier = Suppliers::find($request->id);

        $supplier->title = $request->title;
        $supplier->increaseType = $request->increaseType;
        $supplier->taxType = $request->taxType;
        $supplier->exchange1RUB = $request->exchange;
        $supplier->yurinfo = $request->yurinfo;

        $supplier->update();

        return ['ok' => true, 'result' => []];
    }

    public function deleteSupplier(Request $request)
    {
        $supplier = Suppliers::find($request->id);
        $supplier->active = false;
        $supplier->deleted = true;
        $supplier->update();
        
        return ['ok' => true, 'result' => []];
    }
}
