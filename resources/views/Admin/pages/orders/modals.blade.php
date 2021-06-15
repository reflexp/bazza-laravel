{{-- Порядок модалок изменять НЕЛЬЗЯ, сломаются стили! modalOrderControl, modalOrderAddProduct, modalOrderInfo--}}
<div class="modal fade" id="modalOrderControl"  >
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document"> {{--            modal-dialog-scrollable--}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotyfTitle">Управление заказом</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalOrderAddProduct" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление товара</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="">
                    <div class="form-group form-inline">
                        <input type="text" class="form-control w-50 mx-1" name="searchArticleField" placeholder="Артикул товара">
                        <button type="button" class="btn btn-primary mx-1 btn__find-by-article">Поиск <i class="fa fa-search"></i></button>
                        <input type="hidden" name="orderID">
                        <input type="hidden" name="oldProductID">
                        <input type="hidden" name="type">
                    </div>
                    <table class="table table-striped" id="top5-products">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Завод</th>
                            <th>Поставщик</th>
                            <th>Кол-во</th>
                            <th>Цена</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success btn__set-new-product my-3">Добавить <i class="fa fa-check"></i></button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOrderInfo" >
    <div class="modal-dialog modal-dialog-centered modal-lg " role="document"> {{--            modal-dialog-scrollable--}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotyfTitle">Информация</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalProdRemoveConfirm" >
    <div class="modal-dialog modal-dialog-centered modal-lg " data-backdrop="static"> {{--            modal-dialog-scrollable--}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotyfTitle">Подтверждение</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalBundleCreateConfirm" >
    <div class="modal-dialog modal-dialog-centered modal-lg " data-backdrop="static"> {{--            modal-dialog-scrollable--}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNotyfTitle">Подтверждение</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body row">
                <form action="" class="form-inline">
                    <input type="text" name="comment" class="form-control w-50" placeholder="Комментарий к пакету">
                    <button type="button" class="btn btn-primary mx-2 btn__send-create-orders-bundle"> <i class="fa fa-box-open"></i> Собрать пакет </button>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
{{-- Порядок модалок изменять НЕЛЬЗЯ, сломаются стили! modalOrderControl, modalOrderAddProduct, modalOrderInfo --}}
