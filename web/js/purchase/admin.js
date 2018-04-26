function acceptProductPurchase()
{
    if ($('#created-date').val() == '' || $('#purchase-date').val() == '' || $('#stop-date').val() == '' || $('#purchase-provider_id').val() == '' || $('select[name="product-id"]').val() == '') {
        alert("Ошибка заполнения полей!");
        return false;
    }
    var html = $.ajax({
        url: "/admin/purchase/add-product",
        async: false,
        type: "POST",
        data: $('#addProdPurchase').serialize()
    }).responseText;
    
    $('#purchase-accept-product-modal').modal('hide');
    window.location.reload();
}

function togglePurchaseProductsContainer(status)
{
    if (status == 'show') {
        var html = $.ajax({
            url: "/admin/purchase/get-products",
            async: false,
            type: "POST",
            data: {provider_id: $("#purchase-provider_id").val()}
        }).responseText;
        if (html) {
            $("#purchase-product-container").html(html);
        }
        $("#purchase-product-container").show();
    } else {
        $('#purchase-product-form').html('');
        $("#purchase-product-container").hide();
    }
}

function displayPurchaseProductData(element) {
    var html = $.ajax({
            url: "/admin/purchase/get-product",
            async: false,
            type: "POST",
            data: {product_id: element[element.selectedIndex].value}
        }).responseText;
        if (html) {
            $("#purchase-product-form").html(html);
        }
}

function set_purchase_product_data(obj)
{
    var f_id = $(obj).data('id');
    if (f_id != 0) {
        $.ajax({
            url: "/admin/purchase/get-feature",
            type: "POST",
            data: {id: f_id},
            success: function(response) {
                var data = $.parseJSON(response);
                
                $("#tare-ex").val(data.tare);
                $("#volume-ex").val(data.volume);
                $("#measurement-ex").val(data.measurement);
                $("#summ-ex").val(data.price);
                $("#product-exists").val('1');
                $("#is_weights_ex").val(data.is_weights);
                if (data.is_weights == 1) {
                    $("#weight-lbl").text("Масса/Объём 1 единицы");
                    $("#summ-lbl").text("Сумма за 1 кг./л.");
                }
                
                $("#stock-inner-new").hide();
                $("#stock-inner-exists").show();
            }
        });
    } else {
        $("#stock-inner-exists").hide();
        $("#stock-inner-new").show();
        $("#product-exists").val('0');
    }
}

function changeIsWeightsPurchase(obj)
{
    if (obj.checked) {
        $("#weight-lbl").text("Масса/Объём 1 единицы");
        $("#summ-lbl").text("Сумма за 1 кг./л.");
    } else {
        $("#weight-lbl").text("Масса/Объём");
        $("#summ-lbl").text("Сумма за ед./т.");
    }
}

function change_renewal(elem)
{
    var check = 0;
    if (elem.checked) {
        check = 1;
    }
    var html = $.ajax({
        url: "/admin/purchase/cnange-renewal",
        async: false,
        type: "POST",
        data: {id: $(elem).val(), checked: check}
    }).responseText;
}
