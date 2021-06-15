<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\RequesterController;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Rules\PhoneValidation;

use App\Models\Users;
use App\Models\Roles;
use App\Models\Storages;
use App\Models\Storageusers;

class UserTableController extends Controller
{

    public function index()
    {
        $roles = Roles::all();
        $storages = Storages::all();

        return view('Admin.pages.users.index', [
            'roles' => $roles,
            'storages' => $storages
        ]);
    }

    public function addUser(Request $request)
    {   
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'role' => ['required', 'numeric'],
            'storages' => ['required_if:role, 2', 'array', 'min:1'],
            'storages.*'  => ['required', 'distinct', 'numeric'],
            'name' => ['required', 'string', 'max:128'],
            'login' => ['required', 'string', new PhoneValidation, 'unique:users', 'max:32'],
            'email' => ['required', 'string', 'unique:users', 'max:64'],
            'password' => ['required', 'string', 'max:64'],
        ];

        $validation = $request->validate($validateRules);

        $user = new Users;

        $user->roleID = $request->role;
        $user->name = $request->name;
        $user->login = RequesterController::clearPhone($request->login);
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        $user->save();

        foreach ($request->storages as $storage) {
            $storageuser = new Storageusers;

            $storageuser->userID = $user->id;
            $storageuser->storageID = $storage;

            $storageuser->save();
        }

        return ['ok' => true, 'result' => []];
    }

    public function getUser(Request $request)
    {
        $user = Storageusers::where('userID', $request->id)
            ->join('storages', 'storageusers.storageID', '=', 'storages.id')
            ->select('storages.title', 'storageusers.*')
            ->get();

        return $user;
    }

    public function getUsers()
    {
        $users = Users::all();
        return ['ok' => true, 'result' => ['users' => $users]];
    }

    public function editUser(Request $request)
    {   
        $request->merge(['login' => RequesterController::clearPhone($request->login)]);

        $validateRules = [
            'role' => ['required', 'numeric'],
            'storages' => ['required_if:role, 2', 'array', 'min:1'],
            'storages.*'  => ['required', 'distinct', 'numeric'],
            'name' => ['required', 'string', 'max:128'],
            'login' => ['required', 'string', new PhoneValidation, Rule::unique('users')->ignore($request->id), 'max:32'],
            'password' => ['max:64'],
            'email' => ['required', 'string', Rule::unique('users')->ignore($request->id), 'max:64'],
        ];

        $validation = $request->validate($validateRules);

        $user = Users::find($request->id);

        $user->roleID = $request->role;
        $user->name = $request->name;
        $user->login = RequesterController::clearPhone($request->login);
        $user->email = $request->email;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->update();

        $storageuser = Storageusers::where('userID', $request->id)->delete();

        foreach ($request->storages as $storage) {
            $storageuser = new Storageusers;

            $storageuser->userID = $user->id;
            $storageuser->storageID = $storage;

            $storageuser->save();
        }

        return ['ok' => true, 'result' => []];
    }

    public function getRoles() {
        $roles = Roles::all();
        return $roles;
    }

    public function deleteUser(Request $request)
    {
        $user = Users::find($request->id);
        $user->active = false;
        $user->deleted = true;
        $user->update();

        return ['ok' => true, 'result' => []];
    }

}
