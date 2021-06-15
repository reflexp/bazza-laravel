@extends('Client.layouts.app')

@section('title', 'Чат с экспертом')

@section('styles')

@endsection

@section('content')
<main class="main-container">
  <div class="main__left-column">
    <!-- Navbar -->
    @include('Client.layouts.navbar')

    <!-- Main content -->
    <div class="wrapper">
      <section class="profile-container">
        <!-- Account menu -->
        @include('Client.layouts.account-menu')

        <div class="profile__chat">
          <div class="profile__header">
            <h1>Чат с экспертом</h1>
          </div>

          <div class="profile__messaging">
            <div class="profile__message-empty">
              <h1>Ваши сообщения пустые</h1>
              <p>Если вам нужна помощь вы можете обратиться к эксперту написав сообщение в форме снизу.</p>
            </div>
            
            {{-- <div class="profile__chat-date">23 Марта</div> --}}
          </div>

          <form id="messageForm" class="profile__form" method="POST">
            <textarea class="profile__form-message" name="message" placeholder="Сообщение" autocomplete="off" required></textarea>

            <div class="profile__form-row">
              <div class="form__file">
                <p id="fileTitle"><i class="fad fa-paperclip"></i>Прикрепить файл</p>
                <input type="file" name="file" id="file">
              </div>
              <button class="btn" type="submit">Отправить</button>
            </div>
          </form>
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
  jQuery(document).ready(() => {
    /* Функция обновления чата */
    function updateChat() {
      try {
        /* AJAX запрос на обновление */
        const response = clearApiRequest('getNewMessages');

        if (response.status === 200 && response.responseJSON.length > 0) {
          /* Ощичаем чат если появились новые сообщения */
          jQuery('.profile__messaging').html('');
          /* Убираем оповещение о пустом чате */
          jQuery('.profile__message-empty').addClass('disabled');
          
          /* Проводим сообщения через цикл */
          for(const message of response.responseJSON) {
            /* Вводим островок в случае если это клиент, если нет то администратор */
            if (message.senderClientID) {
              /* Разметка островка отвечающая за клиента */
              jQuery('.profile__messaging').append(`
                <div class="profile__chat-user" id="profile__chat-${ message.id }">
                  <div class="chat__header">
                    <p class="chat__name">${ message.clientInfo.name }</p>
                    <small class="chat__info">
                      <small class="chat__date">${ moment(message.created_at).format('HH:mm') } ${ moment(message.created_at).format('DD.MM.YYYY') }</small>
                    </small>
                  </div>
                  
                  <div class="chat__message">
                    <p>${ message.messageText }</p>
                  </div>
                </div>
              `);
              
              /* Если в данном сообщении есть файл, то выводит его */
              if (message.messageFileID !== null) {
                if (message.file.name.split('.').pop() === 'jpeg' || message.file.name.split('.').pop() === 'jpg' || message.file.name.split('.').pop() === 'png') {
                  jQuery(`#profile__chat-${ message.id } .chat__message p`).before(`<a href="${ message.file.path }" download><img src="${ message.file.path }"></a>`)
                } else {
                  jQuery(`#profile__chat-${ message.id } .chat__message p`).before(`<a href="${ message.file.path }" class="btn" download>Скачать файл ${ message.file.name.split('.').pop() }</a>`)
                }
              }
              /* Если сообщение было прочитано, то добавляет галочку */
              if (message.isRead === 1) {
                jQuery(`#profile__chat-${ message.id } .chat__info`).append(`<small class="chat__read" title="Ваше сообщение было прочитано"><i class="fad fa-check-double"></i></small>`)
              }
              
            } else {
              /* Разметка островка отвечающая за администратора */
              jQuery('.profile__messaging').append(`
                <div class="profile__chat-admin" id="profile__chat-${ message.id }">
                  <div class="chat__header">
                    <p class="chat__name">Менеджер<i class="fad fa-check-circle"></i></p>
                    <small class="chat__date">${ moment(message.created_at).format('HH:mm') } ${ moment(message.created_at).format('DD.MM.YYYY') }</small>
                  </div>
                  
                  <div class="chat__message">
                    <p>${ message.messageText }</p>
                  </div>
                </div>
              `);

              /* Если в данном сообщении есть файл, то выводит его */
              if (message.messageFileID !== null) {
                if (message.file.name.split('.').pop() === 'jpeg' || message.file.name.split('.').pop() === 'jpg' || message.file.name.split('.').pop() === 'png') {
                  jQuery(`#profile__chat-${ message.id } .chat__message p`).before(`<a href="${ message.file.path }" download><img src="${ message.file.path }"></a>`)
                } else {
                  jQuery(`#profile__chat-${ message.id } .chat__message p`).before(`<a href="${ message.file.path }" class="btn" download>Скачать файл ${ message.file.name.split('.').pop() }</a>`)
                }
              }

            }
          }
        }
      } catch (error) {
          console.info(error);
          showNotification('error', error);
      }
    }

    /* Функция отправления сообщения */
    jQuery(document).on('submit', '#messageForm', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      const response = apiRequest('addMessage', formData);

      if (response.ok === true) {
        this.reset();
        updateChat();
        /* Скроллим вниз после каждого обновления */
        jQuery('.profile__messaging').animate({ scrollTop: $(document).height() }, 500);
      }
    });

    /* Функция для обновления надписи инпута */
    jQuery(document).on('change', '#file', function(e) {
      (this.value.slice(12).length > 0) ? jQuery('#fileTitle').html('<i class="fad fa-file"></i>' + this.value.slice(12)) : jQuery('#fileTitle').html('<i class="fad fa-paperclip"></i>Прикрепить файл')
    });

    /* Интервал обновления чата 60 секунд */
    setInterval(updateChat, 60000);

    /* После загрузки страницы сразу обновляем чат */
    updateChat();
    /* Скроллим вниз после каждого обновления */
    jQuery('.profile__messaging').animate({ scrollTop: $(document).height() }, 500);
  });
</script>
@endsection