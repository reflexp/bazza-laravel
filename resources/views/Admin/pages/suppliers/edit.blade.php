@extends('Admin.layouts.app')

@section('title', 'Редактирование поставщика')

@section('styles')

@endsection

@section('content')
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Редактирование поставщика</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <form id="saveSupplyForm">
                            <input type="hidden" name="id" value="{{ $supplier->id }}">
                            <div class="form-group">
                              <label for="title">Наименование поставщика</label>
                              <input type="text" class="form-control" name="title" value="{{ $supplier->title }}" placeholder="Введите наименование">
                            </div>
                              <div class="form-group col-md-6">
                                  <label for="taxType">Склад</label>
                                  <select name="storageID" class="form-control">
                                      @foreach ($storages as $storage)
                                          <option value="{{ $storage->id }}" {{$storage->id == $supplier->storageID ? 'selected' : ''}}>{{ $storage->title }}</option>
                                      @endforeach
                                  </select>
                              </div>
                            <div class="form-row">
                              <div class="form-group col-md-4">
                                <label for="commonTax">Наценка - налоги</label>
                                <input type="number" class="form-control" name="commonTax" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="increaseIncome">Наценка - прибыль</label>
                                <input type="number" class="form-control" name="increaseIncome" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="increaseDelivery">Наценка - доставка</label>
                                <input type="number" class="form-control" name="increaseDelivery" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                            </div>
                            <div class="form-row">
                              <div class="form-group col-md-4">
                                <label for="increaseRetail">Наценка - розничная цена</label>
                                <input type="number" class="form-control" name="increaseRetail" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="increaseWholesale">Наценка - оптовая цена</label>
                                <input type="number" class="form-control" name="increaseWholesale" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                              <div class="form-group col-md-4">
                                <label for="increaseBigWholesale">Наценка - крупнооптовая цена</label>
                                <input type="number" class="form-control" name="increaseBigWholesale" min="1" max="100" placeholder="Проценты - 20%">
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="exchange">Курс рубля в тенге</label>
                              <input type="number" class="form-control" name="exchange" step="0.01" value="{{ $supplier->exchange1RUB }}" placeholder="Пример - 5,65">
                            </div>
                            <div class="form-row">
                              <div class="form-group col-md-6">
                                <label for="increaseType">Тип наценки</label>
                                <select name="increaseType" class="form-control">
                                  <option value="0">Наценка</option>
                                  <option value="1">Дифференцированная наценка</option>
                                </select>
                              </div>
                              <div class="form-group col-md-6">
                                <label for="taxType">Тип налога</label>
                                <select name="taxType" class="form-control">
                                  <option value="0">Упрощенка</option>
                                  <option value="1">Общеустановленный налог</option>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label for="yurinfo">Юридическая информация поставщика</label>
                              <textarea type="text" class="form-control" name="yurinfo" placeholder="Введите юридическую информацию" rows="8" maxlength="2048">{{ $supplier->yurInfo }}</textarea>
                              <small class="form-text text-muted">Количество символов: 2048</small>
                            </div>
                            <div class="text-right">
                              <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                            </div>
                          </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
@endsection

@section('scripts')
    <script>
        window.addEventListener('load', () => {
          jQuery('select[name="increaseType"]').find(`option[value="{{ $supplier->increaseType }}"]`).prop('selected', true);
          jQuery('select[name="taxType"]').find(`option[value="{{ $supplier->taxType }}"]`).prop('selected', true);

          jQuery('textarea[name="yurinfo"]').on('input', function() {
            const length = 2048;
            (this.value.length <= length) ? jQuery(this).next().html(`Количество символов: ${length - this.value.length}`) : jQuery(this).val(this.value.substring(0, this.value.length - 1));
          });

          // Сохранение
          jQuery(document).on('submit', '#saveSupplyForm', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const addData = {'test': 'test'};

            Object.keys(addData).map((current, index) => formData.append(current, addData[current]));

            const response = apiRequest('editSupplier', formData);

            if (response.ok === true) {
              window.location.href = '/control/suppliers';
            }
          });
        });
    </script>
@endsection
