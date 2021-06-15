<script>



    jQuery(document).on('click', '.btn__save-order', function() {
        let orderBlock;
        const formData = new FormData();

        let orderID = jQuery(this).data('order-id');

        if(ordersPage)
        {
            orderBlock = jQuery('#modalOrderControl');
            let comment = orderBlock.find('.orderComment').val();
            let statusPay = orderBlock.find('.orderPayStatus').val();
            formData.append('comment', comment);
            formData.append('statusPay', statusPay);
        }
        else
        {
            orderBlock = jQuery(this).parents('.block__orderInfo');
        }

        let status = orderBlock.find('.orderStatus').val();

        formData.append('orderID', orderID);
        formData.append('status', status);

        let response = apiRequest('editOrder', formData);
        if(response.ok === true) {
            if(ordersPage)
            {
                datatable.ajax.reload();
                jQuery('#modalOrderControl').modal('hide');
            }
            else{
                window.location.reload();
            }
        }

    });

    /*
     * Сохраняет новые данные о товаре в заказе
     * statusID
     * amount
     * */
    jQuery(document).on('click', '.btn__save-product', function() {
        let parentROW = jQuery(this).parents('tr');
        let productID = parentROW.data('product-id');
        let amount = parentROW.find('.productAmount').val();
        let status = parentROW.find('.productStatus').val();

        const formData = new FormData();
        formData.append('productID', productID);
        formData.append('amount', amount);
        formData.append('status', status);

        let response = apiRequest('editOrderProduct', formData);
        if(response.ok === true) {
            if(ordersPage)
            {
                datatable.ajax.reload();
                jQuery('#modalOrderControl').modal('hide');
            }
            else{
                window.location.reload();
            }
        }
    });

    /*
    * Вызывает модальное окно с поиком товаров по номенклатуре, при успешном - заменить и подставить данные.
    * Таблицы не будет, будет только поиск по точному совпадению артикула. ТОП 5.
    * */
    jQuery(document).on('click', '.btn__change-product', function() {
        let productID = jQuery(this).parents('tr').data('product-id');
        jQuery('#modalOrderAddProduct').modal('show');
        jQuery('#modalOrderAddProduct input[name="searchArticleField"]').val('');
        jQuery('#modalOrderAddProduct #top5-products tbody').empty();
    });

    /*
    * Вызывает модалку добавления товара в заказ
    * */
    jQuery(document).on('click', '.btn__showmodal-add-product', function() {
        let modal = jQuery('#modalOrderControl');
        let block = jQuery(this).parents('.block__orderInfo');
        let orderID = modal.data('order-id') ?? block.data('order-id'); // Универсально или из одного блока или из второго

        // let productID = jQuery(this).data('product-id');
        jQuery('#modalOrderAddProduct').modal('show');
        jQuery('#modalOrderAddProduct input[name="searchArticleField"]').val('');
        jQuery('#modalOrderAddProduct #top5-products tbody').empty();

        jQuery('#modalOrderAddProduct input[name="orderID"]').val(orderID);
        jQuery('#modalOrderAddProduct input[name="oldProductID"]').val('');
        jQuery('#modalOrderAddProduct input[name="type"]').val(1);


        // jQuery('#modalOrderAddProduct input[name="oldProductID"]').val(productID);

        // orderID
        // oldProductID
    });

    /*
    * Вызывает модалку замены товара в заказе
    * */
    jQuery(document).on('click', '.btn__showmodal-edit-product', function() {
        let modal = jQuery('#modalOrderControl');
        let block = jQuery(this).parents('.block__orderInfo');
        let orderID = modal.data('order-id') ?? block.data('order-id'); // Универсально или из одного блока или из второго

        let productID = jQuery(this).parents('tr').data('product-id');

        jQuery('#modalOrderAddProduct').modal('show');
        jQuery('#modalOrderAddProduct input[name="searchArticleField"]').val('');
        jQuery('#modalOrderAddProduct #top5-products tbody').empty();

        jQuery('#modalOrderAddProduct input[name="orderID"]').val(orderID);
        jQuery('#modalOrderAddProduct input[name="oldProductID"]').val(productID);

        jQuery('#modalOrderAddProduct input[name="type"]').val(2);

    });

    /*
    * Удаляет товар из заказа, пересчитыват стоимость
    * */
    jQuery(document).on('click', '.btn__remove-product', function() {
        let confirmStatus = confirm('Вы действительно хотите удалить товар из заказа?');

        if(confirmStatus)
        {
            let parentROW = jQuery(this).parents('tr');
            let productID = parentROW.data('product-id');
            const formData = new FormData();
            formData.append('productID', productID);
            let response = apiRequest('removeOrderProduct', formData);
            if (response.ok === true) {
                if(ordersPage)
                {
                    datatable.ajax.reload();
                    jQuery('#modalOrderAddProduct').modal('hide');
                    jQuery('#modalOrderControl').modal('hide');
                }
                parentROW.remove();

            }
        }

    });

    /*
    * Выкидывает товар из пакета
    * */
    jQuery(document).on('click', '.btn__remove_orderbundle', function() {
        let confirmStatus = confirm('Вы действительно хотите убрать заказ из пакета?');
        if(confirmStatus)
        {
            let parentROW = jQuery(this).parents('.block__orderInfo');
            const formData = new FormData();
            formData.append('orderID', jQuery(this).data('order-id'));
            formData.append('bundleID', BUNDLE_ID);
            let response = apiRequest('removeOrderFromBundle', formData);
            console.log(response);
            if (response.ok === true) {
                if(ordersPage)
                {
                    datatable.ajax.reload();
                    jQuery('#modalOrderAddProduct').modal('hide');
                    jQuery('#modalOrderControl').modal('hide');
                }
                parentROW.remove();

            }
        }

    });



    /*
       * Отмечает radio - checked при нажатии на строку в table
       * */
    jQuery(document).on('click', '#top5-products tbody tr', function() {
        jQuery(this).find('input[type="radio"]').attr('checked', true);
    });

    /*
    * Поиск товаров по артикулу
    * */
    jQuery(document).on('click', '.btn__find-by-article', function() {
        let article = jQuery('#modalOrderAddProduct input[name="searchArticleField"]').val();
        jQuery('#modalOrderAddProduct #top5-products tbody').empty();

        const formData = new FormData();
        formData.append('article', article);

        let response = apiRequest('findProductsByArticle', formData);

        if(response.ok === true) {
            jQuery.each(response.result.products, function (i, product) {
                jQuery('#modalOrderAddProduct #top5-products tbody').append(`
                        <tr>
                            <td>
                                <input type="radio" class="form-check-input ml-1" name="newArticle" value="${ product['id'] }">
                            </td>
                            <td>${ product['article'] }</td>
                            <td>${ product['title'] }</td>
                            <td>${ product['manufacturer'] ?? '' }</td>
                            <td>${ product['supplierID'] }</td>
                            <td>${ product['amount'] }</td>
                            <td>${ product['price'] }</td>
                        </tr>
                    `);
            });
        }
        else
        {
            showNotification('error', 'Товары по заданному артикулу не найдены.');
            jQuery('#modalOrderAddProduct #top5-products tbody').append('<tr><td colspan="7">Товары не найдены.</td></tr>');
        }
    });

    /*
    * Выбор товара и обновление или добавление к заказу
    * */
    jQuery(document).on('click', '.btn__set-new-product', function() {
        let modal = jQuery('#modalOrderAddProduct');
        let type = modal.find('[name="type"]').val();
        let orderID = modal.find('[name="orderID"]').val();
        let oldProductID = modal.find('[name="oldProductID"]').val();
        let newArticle = modal.find('[name="newArticle"]:checked').val();

        const formData = new FormData();
        formData.append('orderID', orderID);
        formData.append('oldProductID', oldProductID);
        formData.append('newArticle', newArticle);

        if(parseInt(type) === 1) {
            let response = apiRequest('addOrderProduct', formData);


            if(response.ok === true) {
                if(ordersPage)
                {
                    datatable.ajax.reload();
                    jQuery('#modalOrderAddProduct').modal('hide');
                    jQuery('#modalOrderControl').modal('hide');
                }
                else{
                    window.location.reload();
                }

            }
        }
        else if (parseInt(type) === 2)
        {
            let response = apiRequest('changeOrderProduct', formData);
            if(response.ok === true) {
                if(ordersPage)
                {
                    datatable.ajax.reload();
                    jQuery('#modalOrderAddProduct').modal('hide');
                    jQuery('#modalOrderControl').modal('hide');
                }
                else{
                    window.location.reload();
                }

            }
        }

    });





</script>
