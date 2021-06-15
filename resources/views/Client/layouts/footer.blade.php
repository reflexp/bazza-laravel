<footer class="footer-container">
  <div class="footer__mobile">
    <div class="footer__cart">
      <a href="{{ route('Client.shopcart') }}" class="shopcart-btn"><i class="fad fa-shopping-cart"></i>125 951 тг</a>
    </div>

    <div class="footer__search">
      <a href="{{ route('Client.nomenclature') }}" class="btn"><i class="fad fa-search"></i>Найти запчасть</a>
    </div>
  </div>

  <div class="footer__desktop">
    <div class="wrapper">{{ date('Y') }} © Bazza.kz</div>
  </div>
</footer>