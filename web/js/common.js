$(document).ready(function() {
    $(".deposit-check").change(function() {
        change_deposit(this);
    });
    
});

function toggleCategoriesContainer(status)
{
    if (status == 'show') {
        var html = $.ajax({
            url: "/admin/product/get-categories",
            async: false,
            type: "POST",
            data: {provider_id: $("#product-provider_id").val()}
        }).responseText;
        if (html) {
            $("#product-categories-container").html(html);
        }
        $("#product-categories-container").show();
    } else {
        $("#product-categories-container").hide();
    }
}

function toggleProductsContainer(status)
{
    if (status == 'show') {
        var html = $.ajax({
            url: "/admin/stock/get-products",
            async: false,
            type: "POST",
            data: {provider_id: $("#stockhead-provider_id").val()}
        }).responseText;
        if (html) {
            $("#stockhead-product-container").html(html);
        }
        $("#stockhead-product-container").show();
    } else {
	$('#stockhead-product-form').html('');
        $("#stockhead-product-container").hide();
    }
}

function displayProductData(element) {
	var html = $.ajax({
            url: "/admin/stock/get-product",
            async: false,
            type: "POST",
            data: {product_id: element[element.selectedIndex].value}
        }).responseText;
        if (html) {
            $("#stockhead-product-form").html(html);
        }
}

function acceptProduct() {
	if($('#stockhead-who').val() == '' || $('#stockhead-date').val() == ''|| $('#stockhead-provider_id').val() == '' || $('select[name="product-id"]').val() == '') {
		alert("Ошибка заполнения полей!");
		return false;
	}
        var html = $.ajax({
            url: "/admin/stock/add-product",
            async: false,
            type: "POST",
            data: $('#addProd').serialize()
        }).responseText;
        if (html) {
            $("#stock-list").html(html);
        }
	$('#accept-product-modal').modal('hide');
}

function toggleNewPrice(elem)
{
    if (elem.checked) {
        $("#summ").prop("readonly", false);
        //$("#add-to-avail-container").show();
    } else {
        $("#summ").prop("readonly", true);
        //$("#add-to-avail-container").hide();
    }
}

function change_deposit(elem)
{
    var check = 0;
    if (elem.checked) {
        check = 1;
    }
    var html = $.ajax({
        url: "/admin/stock/cnange-deposit",
        async: false,
        type: "POST",
        data: {id: $(elem).val(), checked: check}
    }).responseText;
}