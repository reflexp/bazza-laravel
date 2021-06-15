<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Rules\PhoneValidation;

use App\Models\Storages;
use App\Models\Cities;

class StorageController extends Controller
{
    public function index() {
        $cities = Cities::all();
        return view('Admin.pages.storages.index', [
            'cities' => $cities
        ]);
    }

    public function getCities() {
        $cities = Cities::all();
        return $cities;
    }
    
    public function getStorage(Request $request) {
        $storage = Storages::findOrFail($request->id);
        return $storage;
    }

    public function getStorages() {
        $storages = Storages::all();
        return ['ok' => true, 'result' => ['storages' => $storages]];
    }

    public function addStorage(Request $request)
    {   
        $request->merge(['contactPhone' => RequesterController::clearPhone($request->contactPhone)]);

        $validateRules = [
            'title' => ['required', 'string', 'max:512'],
            'city' => ['required', 'numeric'],
            'address' => ['required', 'string', 'max:512'],
            'contactPhone' => ['required', 'string', new PhoneValidation, 'unique:storages', 'max:16'],
        ];

        $validation = $request->validate($validateRules);

        $storage = new Storages;

        $storage->title = $request->title;
        $storage->cityID = $request->city;
        $storage->address = $request->address;
        $storage->contactPhone = RequesterController::clearPhone($request->contactPhone);
        
        $storage->save();

        return ['ok' => true, 'result' => []];
    }

    public function editStorage(Request $request)
    {   
        $request->merge(['contactPhone' => RequesterController::clearPhone($request->contactPhone)]);

        $validateRules = [
            'title' => ['required', 'string', 'max:512'],
            'city' => ['required', 'numeric'],
            'address' => ['required', 'string', 'max:512'],
            'contactPhone' => ['required', 'string', new PhoneValidation, Rule::unique('storages')->ignore($request->id), 'max:16'],
        ];

        $validation = $request->validate($validateRules);

        $storage = Storages::find($request->id);

        $storage->title = $request->title;
        $storage->cityID = $request->city;
        $storage->address = $request->address;
        $storage->contactPhone = RequesterController::clearPhone($request->contactPhone);

        $storage->update();

        return ['ok' => true, 'result' => []];
    }

    public function deleteStorage(Request $request)
    {
        $storage = Storages::find($request->id);
        $storage->active = false;
        $storage->deleted = true;
        $storage->update();
        
        return ['ok' => true, 'result' => []];
    }
}
