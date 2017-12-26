$(document).ready(function() {
    $(".deposit-check").change(function() {
        change_deposit(this);
    });
    $("#add-fund-btn").click(function() {
        $("#add-fund-cnt").show();
        $("#fund-name").val('');
        $("#fund-percent").val('');
        $("#save-fund-btn").show();
        $("#cancel-fund-btn").show();
        $("#add-fund-btn").hide();
    });
    
    $("#cancel-fund-btn").click(function() {
        $("#add-fund-cnt").hide();
        $("#save-fund-btn").hide();
        $("#cancel-fund-btn").hide();
        $("#add-fund-btn").show();
        $("#del-fund-btn").hide();
        $("#fund-name-tbl tr").css({'background-color' : '#fff'});
        $("#save-fund-btn").data('action', 'add').attr('data-action', 'add');
        $("#save-fund-btn").data('id', '').attr('data-id', '');
        $("#del-fund-btn").data('id', '').attr('data-id', '');
    });
    
    $("#save-fund-btn").click(function() {
        if ($(this).data('action') == 'add') {
            $.ajax({
                url: "/admin/fund/add",
                type: "POST",
                data: {name: $("#fund-name").val(), percent: $("#fund-percent").val()},
                success: function(response) {
                    $.pjax.reload({container:"#fund-name-pjax"});
                    $("#fund-name-pjax").on('pjax:complete', function() {
                        $("#add-fund-cnt").hide();
                        $("#save-fund-btn").hide();
                        $("#cancel-fund-btn").hide();
                        $.pjax.reload({container:"#fund-deduction-pjax"});
                    })
                }
            });
        } else {
            $.ajax({
                url: "/admin/fund/update",
                type: "POST",
                data: {id: $(this).data('id'),name: $("#fund-name").val(), percent: $("#fund-percent").val()},
                success: function(response) {
                    $.pjax.reload({container:"#fund-name-pjax"});
                    $("#fund-name-pjax").on('pjax:complete', function() {
                        $("#add-fund-cnt").hide();
                        $("#save-fund-btn").hide();
                        $("#cancel-fund-btn").hide();
                        $("#save-fund-btn").data('action', 'add').attr('data-action', 'add');
                        $("#save-fund-btn").data('id', '').attr('data-id', '');
                        $("#add-fund-btn").show();
                        $("#del-fund-btn").hide();
                        $.pjax.reload({container:"#fund-deduction-pjax"});
                    })
                }
            });
        }
    });
    
    $("#del-fund-btn").click(function() {
        if (confirm('Вы уверены, что желаете удалить Фонд?')) {
            $.ajax({
                url: "/admin/fund/delete",
                type: "POST",
                data: {id: $(this).data('id')},
                success: function(response) {
                    $.pjax.reload({container:"#fund-name-pjax"});
                    $("#fund-name-pjax").on('pjax:complete', function() {
                        $("#add-fund-cnt").hide();
                        $("#save-fund-btn").hide();
                        $("#cancel-fund-btn").hide();
                        $("#add-fund-btn").show();
                        $("#del-fund-btn").hide();
                        $("#del-fund-btn").data('id', '').attr('data-id', '');
                        $.pjax.reload({container:"#fund-deduction-pjax"});
                    })
                }
            });
        }
    });
    
    $(".update-price-modal").click(function() {
        var f_id = $(this).data('id');
        $.ajax({
            url: "/admin/product/get-fund",
            type: "POST",
            data: {id: f_id},
            success: function(response) {
                var data = $.parseJSON(response);
                
                if (data != 0) {
                    $.each(data, function() {
                        $.each(this, function(i, el) {
                            $("[data-fund-id="+i+"]").val(el);
                            $("[data-fund-id="+i+"]").attr('data-feature-id', f_id);
                        });
                    });
                }
            }
        });
        $.ajax({
            url: "/admin/product/get-common-price",
            type: "POST",
            data: {id: f_id},
            success: function(response) {
                var data = $.parseJSON(response);
                $("#fund_common_price_input").val(data.price);
                if (data.fixed == 1) {
                    $("#fund_common_price_check").prop("checked", false);
                    $("#fund_common_price_input").prop('readonly', false);
                } else {
                    $("#fund_common_price_check").prop("checked", true);
                }
            }
        });
    });
    
    $("#fund_common_price_check").change(function() {
        if (this.checked) {
            $("#fund_common_price_input").prop('readonly', true);
        } else {
            $("#fund_common_price_input").prop('readonly', false);
        }
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
        $("#summ-ex").prop("readonly", false);
        //$("#add-to-avail-container").show();
    } else {
        $("#summ-ex").prop("readonly", true);
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

function set_product_data(obj)
{
    var f_id = $(obj).data('id');
    if (f_id != 0) {
        $.ajax({
            url: "/admin/stock/get-feature",
            type: "POST",
            data: {id: f_id},
            success: function(response) {
                var data = $.parseJSON(response);
                
                $("#tare-ex").val(data.tare);
                $("#volume-ex").val(data.volume);
                $("#measurement-ex").val(data.measurement);
                $("#summ-ex").val(data.price);
                $("#product-exists").val('1');
                
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

function updatePrice()
{
    var el_len = $(".fund_percent_input").length;
    $(".fund_percent_input").each(function(index, element) {
        $.ajax({
            url: "/admin/product/set-percent",
            type: "POST",
            data: {f_id: $(this).attr('data-feature-id'), fund_id: $(this).attr('data-fund-id'), percent: $(this).val()},
            success: function(response) {
                if (el_len == index + 1) {
                    var f_id = $(".fund_percent_input").attr('data-feature-id');
                    var fixed = !$("#fund_common_price_check").prop("checked");
                        
                    $.ajax({
                        url: "/admin/product/set-common-price",
                        type: "POST",
                        data: {f_id: f_id, price: $("#fund_common_price_input").val(), fixed: fixed},
                        success: function(response) {
                            $.ajax({
                                url: "/admin/product/get-prices",
                                type: "POST",
                                data: {f_id: f_id},
                                success: function(response) {
                                    var data = $.parseJSON(response);
                                    
                                    $("[data-f-a-id="+f_id+"]").html(data.price);
                                    $("[data-f-m-id="+f_id+"]").html(data.member_price);
                                }
                            });
                            $('#update-price-modal').modal('hide');
                        }
                    });
                }
            }
        });
    });
}

function transferFundFrom()
{
    $.ajax({
        url: "/admin/fund/transfer",
        type: "POST",
        data: {from_id: $("#amount-from-input").val(), to_id: $("#fund-to-select").val(), amount: $("#amount-to").val()},
        success: function(response) {
            
        }
    });
    
    $('#transfer-from-modal').modal('hide');
    $('#transfer-from-modal').on('hidden.bs.modal', function (e) {
        $.pjax.reload({container:"#fund-deduction-pjax"});
    });
}

function transferFundTo()
{
    $.ajax({
        url: "/admin/fund/transfer",
        type: "POST",
        data: {from_id: $("#fund-from-select").val(), to_id: $("#amount-to-input").val(), amount: $("#amount-from").val()},
        success: function(response) {
            
        }
    });
    
    $('#transfer-to-modal').modal('hide');
    $('#transfer-to-modal').on('hidden.bs.modal', function (e) {
        $.pjax.reload({container:"#fund-deduction-pjax"});
    });
}