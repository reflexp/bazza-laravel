<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Storageusers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $adminInfo = $request->get('adminInfo');
        $userInStorages = Storageusers::
            where('userID', $adminInfo['id'])
            ->where('active', 1)
            ->where('deleted', 0)
            ->join('storages', 'storageusers.storageID', 'storages.id')
            ->join('cities', 'storages.cityID', 'cities.id')
            ->select(
                'cities.title as cityTitle',
                'cities.regionTitle as cityRegionTitle',
                'cities.countryTitle as cityCountryTitle',
                'storages.id as storageID',
                'storages.title',
                'storages.address',
                'storages.contactPhone'
            )
            ->get();
        return view('Admin.pages.home.index', compact('adminInfo', 'userInStorages'));
    }

}
