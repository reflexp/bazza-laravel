@extends('Client.layouts.app')

@section('title', 'Главная')

@section('styles')

@endsection

@section('content')
<main class="main-container">
    <div class="main__left-column">
      <!-- Navbar -->
      @include('Client.layouts.navbar')

      <!-- Main content -->
      <div class="wrapper">
        <section class="search-container">
          <div class="search__header">
            <h1><i class="fad fa-search"></i>Поиск автозапчастей</h1>
          </div>

          <div class="search__form">
            <form id="searchForm" method="POST">
              <div class="search__form-row">
                <input type="text" name="name" placeholder="Название автозапчасти">
                <input type="text" name="article" placeholder="Поиск по артикулу">
                <select name="model">
                  <option>Выберите модель авто</option>
                    <option value="ВАЗ">ВАЗ</option>
                    <option value="УАЗ">УАЗ</option>
                    <option value="ГАЗ">ГАЗ</option>
                    <option value="ПАЗ">ПАЗ</option>
                    <option value="Тракторы">Тракторы</option>
                </select>
              </div>
              <button class="btn" type="submit">Найти запчасть</button>
            </form>

            <div class="search__text-helper">
              <a href="#">Не можете найти запчасть?</a>
            </div>
          </div>
        </section>
      </div>

      <!-- Footer -->
      @include('Client.layouts.footer')
    </div>

    <div class="main__right-column">
        @include('Client.layouts.aside')
    </div>
</main>
@endsection

@section('scripts')
<script>
  jQuery(document).on('submit', '#searchForm', function(e) {
    e.preventDefault();
    // Параметры для создания URL строки
    let = query = {
      search: 1,
      model: jQuery('#searchForm').find('select[name="model"]').val(),
      article: jQuery('#searchForm').find('input[name="article"]').val(),
      name: jQuery('#searchForm').find('input[name="name"]').val(),
      start: '0',
      length: '10',
      elemCount: '10',
      sortField: '0',
      sortType: 'asc',
    };
    // Генерация URL строки и вставка их в route
    const queryStr = serializeQueryString(query);
    history.pushState('empty', 'Title', `/nomenclature?${queryStr}`);
    window.location.reload();
  });
</script>
@endsection
