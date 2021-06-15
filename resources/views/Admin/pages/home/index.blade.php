@extends('Admin.layouts.app')

@section('title', 'Домашняя страница')

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h1>Главная страница</h1>

                    <p>
                        Вы: <b>{{ $adminInfo['name'] ?? '--' }}</b>
                        <br>
                        Ваша должность: <b>{{ $adminInfo['roleTitle'] ?? '---' }}</b>
                        <br>
                        <br>
                        Вы привязаны к складам:
                    </p>
                </div>
            </div>
            <div class="row">
{{--                {{ $userInStorages }}--}}
                @foreach($userInStorages as $storage)
                    <div class="col-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title text-bold"> {{ $storage['title'] }} </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text">{{ $storage['cityTitle'] }},<br> <small><i>({{ $storage['cityRegionTitle'] }}, {{ $storage['cityCountryTitle'] }})</i></small></p>
                                <p>Адрес: <b>{{ $storage['address'] }}</b></p>
                                <p>Телефон: <b>{{ $storage['contactPhone'] }}</b></p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
            </div>
        </div>
    </section>




@endsection
