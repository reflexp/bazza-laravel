<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Rules\PhoneValidation;

use App\Models\Clients;

class ClientController extends Controller
{
    public function index()
    {   
        return view('Admin.pages.clients.index');
    }

    public function addClient(Request $request)
    {  
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'name' => ['required', 'string', 'max:128'],
            'login' => ['required', 'string', new PhoneValidation, 'unique:clients', 'max:32'],
            'email' => ['required', 'string', 'unique:clients', 'max:64'],
            'password' => ['required', 'string', 'max:64'],
        ];

        $validation = $request->validate($validateRules);

        $client = new Clients;

        $client->name = $request->name;
        $client->login = RequesterController::clearPhone($request->login);
        $client->email = $request->email;
        $client->password = Hash::make($request->password);
        
        $client->save();

        return ['ok' => true, 'result' => []];
    }

    public function getClient(Request $request)
    {
        $clients = Clients::findOrFail($request->id);
        return $clients;
    }

    public function getClients()
    {
        $clients = Clients::all();
        return ['result' => ['clients' => $clients]];
    }

    public function editClient(Request $request)
    {   
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'name' => ['required', 'string', 'max:128'],
            'login' => ['required', 'string', new PhoneValidation, Rule::unique('clients')->ignore($request->id), 'max:32'],
            'email' => ['required', 'string', Rule::unique('clients')->ignore($request->id), 'max:64'],
        ];

        // Функция валидации входящих данных из формы
        $validated = $request->validate($validateRules);

        $client = Clients::find($request->id);

        $client->name = $request->name;
        $client->login = RequesterController::clearPhone($request->login);
        $client->email = $request->email;

        if (!empty($request->password)) {
            $client->password = Hash::make($request->password);
        }

        $client->update();

        return ['ok' => true, 'result' => []];
    }

    public function deleteClient(Request $request)
    {
        $client = Clients::find($request->id);
        $client->active = false;
        $client->deleted = true;
        $client->update();
        
        return ['ok' => true, 'result' => []];
    }
}
