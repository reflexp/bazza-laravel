@extends('Client.layouts.app')

@section('title', 'example')

@section('styles')

@endsection

@section('content')
<main class="main-container">
    <div class="main__left-column">
      <!-- Navbar -->
      @include('Client.layouts.navbar')

      <!-- Main content -->
      <div class="wrapper">
        <section class="example-container">
          <div class="header-row">
            <div class="breadcrumbs-container">
              <a href="#">Главная</a>
              <a href="#">Каталог</a>
              <a href="#">Как заказать</a>
              <a href="#">Информация</a>
              <a href="#">Склады и магазины</a>
              <a href="#">Статичная страница</a>
            </div>
          </div>

          <div class="left-column">
            <div class="example__header">
              <h1>Voluptatem numquam</h1>
            </div>

            <div class="example__text">
              <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Aut deserunt voluptatibus vel facilis repellendus natus nobis cumque maiores unde consectetur nisi, sapiente impedit? Minima delectus saepe quam quod, nulla molestias.</p>

              <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Deleniti minus vitae necessitatibus quis, incidunt voluptates modi, eius consectetur inventore eum laboriosam laudantium odio libero labore voluptate, ullam assumenda amet dolorum?
              Vel sed dicta nisi omnis blanditiis! Nam, quae perspiciatis eos, mollitia possimus similique illum itaque odio harum recusandae at consequatur quis, eaque ea! Voluptate perspiciatis et sapiente, ea architecto voluptates!
              Voluptatem repellendus, repudiandae minus ducimus deleniti, blanditiis ipsum velit quo fugit unde accusantium iure eveniet, rem mollitia quisquam voluptates libero sequi nobis! Temporibus omnis deleniti, voluptatem numquam earum aperiam dignissimos.
              Aliquam animi vero, itaque eos consequatur rerum illum amet doloremque assumenda cum ratione vel ipsum eum esse voluptatem temporibus veritatis atque fugiat omnis! Dolorem iure adipisci, similique tenetur excepturi sed?
              Animi incidunt sint voluptates ducimus molestiae modi, quos eligendi? Esse hic minus, provident earum quas amet sit rerum numquam, commodi voluptatibus cum accusamus magni suscipit in iure soluta et tenetur.</p>
            </div>
          </div>

          <div class="right-column">
            <div class="example__header">
              <h1>Necessitatibus quis</h1>
            </div>

            <div class="example__text">
              <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Dolores, assumenda doloremque distinctio, reprehenderit consectetur error sint a consequuntur ex excepturi dolorum provident. Molestias maxime natus nemo earum voluptates repellat beatae.
              Eum optio impedit et, error minus aut repellat delectus earum voluptatum quae repudiandae totam accusamus omnis facilis ratione nemo natus assumenda soluta necessitatibus illo, sint odit, rerum nam. Sequi, odit.
              Excepturi quod neque tempore quaerat repellendus dicta ab, maiores aperiam, minus velit accusamus voluptates sequi expedita asperiores ea quibusdam corrupti facilis maxime! Fugit modi temporibus quia accusantium ab ipsa molestias.</p>
            </div>

            <div class="example__header">
              <h1>Cumque maiores</h1>
            </div>

            <div class="example__text">
              <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Aut deserunt voluptatibus vel facilis repellendus natus nobis cumque maiores unde consectetur nisi, sapiente impedit? Minima delectus saepe quam quod, nulla molestias.</p>

              <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Deleniti minus vitae necessitatibus quis, incidunt voluptates modi, eius consectetur inventore eum laboriosam laudantium odio libero labore voluptate, ullam assumenda amet dolorum?
              Vel sed dicta nisi omnis blanditiis! Nam, quae perspiciatis eos, mollitia possimus similique illum itaque odio harum recusandae at consequatur quis, eaque ea! Voluptate perspiciatis et sapiente, ea architecto voluptates!
              Voluptatem repellendus, repudiandae minus ducimus deleniti, blanditiis ipsum velit quo fugit unde accusantium iure eveniet, rem mollitia quisquam voluptates libero sequi nobis! Temporibus omnis deleniti, voluptatem numquam earum aperiam dignissimos.
              Aliquam animi vero, itaque eos consequatur rerum illum amet doloremque assumenda cum ratione vel ipsum eum esse voluptatem temporibus veritatis atque fugiat omnis! Dolorem iure adipisci, similique tenetur excepturi sed?
              Animi incidunt sint voluptates ducimus molestiae modi, quos eligendi? Esse hic minus, provident earum quas amet sit rerum numquam, commodi voluptatibus cum accusamus magni suscipit in iure soluta et tenetur.</p>
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
      let query;
      query = {
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
      history.pushState('empty', 'Title', `/nomenclature/search?${queryStr}`);
      window.location.reload();
    });
  </script>
@endsection
