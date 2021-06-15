<?php

use Illuminate\Support\Facades\Route;

/**
 * Клиентская часть
 **/
Route::get('/refreshToken', 'RequesterController@getToken');

Route::group([
    'namespace' => 'Client',
], function() {
    // Главная страница
    Route::get('/', 'HomeController@index')->name('Client.index');

    // Страница авторизации
    Route::get('/login', 'AuthController@loginPage')->name('Client.login');
    Route::get('/signup', 'AuthController@signupPage')->name('Client.signup');

    // Сброс пароля
    Route::get('/reset', 'AuthController@resetPage')->name('Client.reset');

    // Страница выхода
    Route::get('/logout', 'AuthController@logout')->name('Client.logout');

    Route::get('/nomenclature', 'NomenclatureController@index')->name('Client.nomenclature');

    Route::get('/suppliers', 'SupplierController@index')->name('Client.suppliers');

    Route::get('/shopcart', 'ShopcartController@indexPage')->name('Client.shopcart');

    Route::get('/example', 'ExampleController@examplePage')->name('Client.example');

    Route::group(['prefix' => 'ajax'], function() {
        // Авторизация
        Route::post('/login', 'AuthController@login');
        Route::post('/signup', 'AuthController@signup');
        Route::post('/verify', 'AuthController@verify');

        // Сброс пароля
        Route::post('/reset', 'AuthController@reset');
        Route::post('/verifyReset', 'AuthController@verifyReset');
        Route::post('/editPasswordReset', 'AuthController@editPasswordReset');

        // Страница /nomenclature
        Route::post('/getNomenclatures', 'NomenclatureController@getNomenclatures');
        Route::post('/addToCart', 'ShopcartController@addToCart');
        Route::post('/getCartCount', 'ShopcartController@getCartCount');
        
        // Страница /shopcart
        Route::post('/editCartAmount', 'ShopcartController@editCartAmount');
        Route::post('/removeFromCart', 'ShopcartController@removeFromCart');
    });

    // Страницы личного кабинета и контроль по авторизации
    Route::group(['middleware' => 'ClientSession'], function() {
        Route::get('/account', 'AccountController@indexPage')->name('Client.account');
        Route::get('/account/orders', 'AccountController@ordersPage')->name('Client.account.orders');
        Route::get('/account/chat', 'AccountController@chatPage')->name('Client.account.chat');
        Route::get('/neworder', 'OrdersController@indexPage')->name('Client.order');

        Route::group(['prefix' => 'ajax'], function() {
            /* Страница /account */
            Route::post('/editAccount', 'AccountController@editAccount');
            Route::post('/editPassword', 'AccountController@editPassword');

            /* Страница /neworders */
            Route::post('/addOrder', 'OrdersController@addOrder');

            /* Страница /chat */
            Route::post('/addMessage', 'ChatController@addMessage');
            Route::post('/getNewMessages', 'ChatController@getNewMessages');
        });
    });
});

/**
 * Админская часть
 **/
Route::group([
    'prefix' => 'control',
    'namespace' => 'Admin',
], function() {

    Route::get('/login', 'AuthController@loginPage')->name('Admin.login');
    Route::get('/logout', 'AuthController@logout')->name('Admin.logout');

    Route::post('/ajax/login', 'AuthController@login');

    Route::group(['middleware' => 'AdminSession'], function() {

        Route::get('/', 'HomeController@index')->name('Admin.home');
        Route::get('/clients', 'ClientController@index')->name('Admin.clients')->middleware('AdminRoles:clients');

        Route::get('/users', 'UserTableController@index')->name('Admin.users')->middleware('AdminRoles:users');

        Route::get('/nomenclature', 'NomenclatureTableController@index')->name('Admin.nomenclature')->middleware('AdminRoles:nomenclature');

        Route::get('/orders', 'OrdersController@indexPage')->name('Admin.orders')->middleware('AdminRoles:orders');
        Route::get('/ordersbundles', 'OrdersBundlesController@indexPage')->name('Admin.ordersbundles')->middleware('AdminRoles:ordersbundles');
        Route::get('/ordersbundles/{id}', 'OrdersBundlesController@orderbundleInfoPage')->name('Admin.ordersbundles.info')->middleware('AdminRoles:ordersbundles');

        Route::get('/storages', 'StorageController@index')->name('Admin.storages')->middleware('AdminRoles:storages');

        Route::get('/suppliers', 'SupplyController@index')->name('Admin.suppliers')->middleware('AdminRoles:suppliers');
        Route::get('/suppliers/create', 'SupplyController@create')->name('Admin.suppliers.create')->middleware('AdminRoles:suppliers');
        Route::get('/suppliers/edit/{id}', 'SupplyController@edit')->name('Admin.suppliers.edit')->middleware('AdminRoles:suppliers');

        Route::get('/chat', 'ChatController@indexPage')->name('Admin.chat')->middleware('AdminRoles:chat');

        Route::group(['prefix' => 'ajax'], function() {
            /* Страница /clients */
            Route::post('/addClient', 'ClientController@addClient');
            Route::post('/getClient', 'ClientController@getClient');
            Route::post('/getClients', 'ClientController@getClients');
            Route::post('/editClient', 'ClientController@editClient');
            Route::post('/deleteClient', 'ClientController@deleteClient');

            /* Страница /users */
            Route::post('/addUser', 'UserTableController@addUser');
            Route::post('/getUser', 'UserTableController@getUser');
            Route::post('/getUsers', 'UserTableController@getUsers');
            Route::post('/editUser', 'UserTableController@editUser');
            Route::post('/deleteUser', 'UserTableController@deleteUser');
            Route::post('/getRoles', 'UserTableController@getRoles');

            /* Страница /nomenclature */
            Route::post('/getNomenclatures', 'NomenclatureTableController@getNomenclatures');
            Route::post('/uploadFile/nomenclature', 'NomenclatureTableController@uploadFile');

            /* Страница /orders */
            Route::post('/getOrders', 'OrdersController@getOrders');
            Route::post('/editOrder', 'OrdersController@editOrder');
            Route::post('/editOrderProduct', 'OrdersController@editOrderProduct');
            Route::post('/changeOrderProduct', 'OrdersController@changeOrderProduct');
            Route::post('/addOrderProduct', 'OrdersController@addOrderProduct');
            Route::post('/removeOrderProduct', 'OrdersController@removeOrderProduct');
            Route::post('/findProductsByArticle', 'OrdersController@findProductsByArticle');

            /* Страница /ordersbundles */
            Route::post('/getOrdersFromBundle', 'OrdersBundlesController@getOrdersFromBundle');
            Route::post('/getOrdersBundles', 'OrdersBundlesController@getOrdersBundles');
            Route::post('/createOrdersBundle', 'OrdersBundlesController@createOrdersBundle');
            Route::post('/getSuppliersFromBundle', 'OrdersBundlesController@getSuppliersFromBundle');
            Route::post('/removeOrderFromBundle', 'OrdersBundlesController@removeOrderFromBundle');

            /* Страница /storages */
            Route::post('/addStorage', 'StorageController@addStorage');
            Route::post('/getStorage', 'StorageController@getStorage');
            Route::post('/getStorages', 'StorageController@getStorages');
            Route::post('/editStorage', 'StorageController@editStorage');
            Route::post('/deleteStorage', 'StorageController@deleteStorage');
            Route::post('/getCities', 'StorageController@getCities');

            /* Страница /suppliers */
            Route::post('/addSupplier', 'SupplyController@addSupplier');
            Route::post('/getSupplier', 'SupplyController@getSupplier');
            Route::post('/getSuppliers', 'SupplyController@getSuppliers');
            Route::post('/editSupplier', 'SupplyController@editSupplier');
            // Route::post('/deleteClient', 'SupplyController@deleteClient');

            /* Страница /chat */
            Route::post('/getDialogs', 'ChatController@getDialogs');
            Route::post('/addMessage', 'ChatController@addMessage');
            Route::post('/getNewMessages', 'ChatController@getNewMessages');
        });
    });

});
