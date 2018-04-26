$(document).ready(function() {
    if ($("#request-active").val() == 1) {
        $("#user-menu-lnk a.dropdown-toggle").addClass('blink');
        $("#request-menu-lnk a").addClass('blink1');
    }
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
    
    $("#product-change-provider").change(function() {
        if (this.checked) {
            $("#product-provider-exists-container").hide();
            $("#product-provider-change-container").show();
        } else {
            $("#product-provider-exists-container").show();
            $("#product-provider-change-container").hide();
        }
    });
    
    $("#add-candidate-btn").click(function() {
        $("#add-candidate-modal .modal-dialog .modal-content .modal-body").load($(this).attr("href"));
    });
    
    $(".update-group-btn").click(function() {
        $("#update-group-name-txt").val($(this).attr('data-name'));
        $("#update-group-frm").attr('action', '/admin/candidate-group/update?id=' + $(this).attr('data-id'));
    })
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
                $("#is_weights_ex").val(data.is_weights);
                if (data.is_weights == 1) {
                    $("#weight-lbl").text("Масса/Объём 1 единицы");
                    $("#count-lbl").text("Общий принимаемый вес");
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
        data: {from_id: $("#amount-from-input").val(), amount: $("#amount-to").val()},
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

function detalization()
{
    if ($("#purchase-details-btn").hasClass("closed")) {
        var html = $.ajax({
            url: "/admin/provider-order/get-detalization",
            async: false,
            type: "POST",
            data: {date: $("#details-date").val()}
        }).responseText;
        if (html) {
            $("#purchase-details-btn").removeClass("closed");
            $("#purchase-details-btn").addClass("opened");
            $("#purchase-details-btn").text('Свернуть');
            $("#purchase-details-container").html(html);
            $("#purchase-details-container").slideDown(500);
        }
    } else {
        $("#purchase-details-btn").removeClass("opened");
        $("#purchase-details-btn").addClass("closed");
        $("#purchase-details-btn").text('Детализация');
        //$("#purchase-details-container").html("");
        $("#purchase-details-container").slideUp();
        $.ajax({
            url: "/admin/provider-order/show-all",
            type: "POST",
            data: {date: $("#details-date").val()},
            success: function(response) {
                
            }
        });
    }
}

function detalizationStock()
{
    if ($("#purchase-details-btn").hasClass("closed")) {
        var html = $.ajax({
            url: "/admin/order/get-detalization",
            async: false,
            type: "POST",
            data: {date_e: $("#details-date-end").val(), date_s: $("#details-date-start").val()}
        }).responseText;
        if (html) {
            $("#purchase-details-btn").removeClass("closed");
            $("#purchase-details-btn").addClass("opened");
            $("#purchase-details-btn").text('Свернуть');
            $("#purchase-details-container").html(html);
            $("#purchase-details-container").slideDown(500);
        }
    } else {
        $("#purchase-details-btn").removeClass("opened");
        $("#purchase-details-btn").addClass("closed");
        $("#purchase-details-btn").text('Детализация');
        //$("#purchase-details-container").html("");
        $("#purchase-details-container").slideUp();
        $.ajax({
            url: "/admin/order/show-all",
            type: "POST",
            data: {date_e: $("#details-date-end").val(), date_s: $("#details-date-start").val()},
            success: function(response) {
                
            }
        });
    }
}

function hideOrder(obj)
{
    var html = $.ajax({
        url: "/admin/provider-order/hide",
        async: false,
        type: "POST",
        data: {o_id: $(obj).attr("data-order-id"), date: $(obj).attr("data-date")}
    }).responseText;
    if (html) {
        $("#purchase-details-container").html(html);
    }
}

function hideOrderStock(obj)
{
    var html = $.ajax({
        url: "/admin/order/hide",
        async: false,
        type: "POST",
        data: {o_id: $(obj).attr("data-order-id"), date_e: $(obj).attr("data-date-e"), date_s: $(obj).attr("data-date-s")}
    }).responseText;
    if (html) {
        $("#purchase-details-container").html(html);
    }
}

function setPageView()
{
    var html = $.ajax({
        url: "/admin/provider-order/set-view",
        async: false,
        type: "POST",
        data: {date: $("#details-date").val()}
    }).responseText;
    if (html) {
        $("#purchase-details-btn").removeClass("closed");
        $("#purchase-details-btn").addClass("opened");
        $("#purchase-details-btn").text('Свернуть');
        $("#purchase-details-container").html(html);
        $("#purchase-details-container").slideDown(500);
    }
}

function setPageViewStock()
{
    var html = $.ajax({
        url: "/admin/order/set-view",
        async: false,
        type: "POST",
        data: {date_e: $("#details-date-end").val(), date_s: $("#details-date-start").val()}
    }).responseText;
    if (html) {
        $("#purchase-details-btn").removeClass("closed");
        $("#purchase-details-btn").addClass("opened");
        $("#purchase-details-btn").text('Свернуть');
        $("#purchase-details-container").html(html);
        $("#purchase-details-container").slideDown(500);
    }
}

function deleteOrder(obj)
{
    if (confirm('Вы уверены, что хотите удалить этот заказ?')) {
        $.ajax({
            url: "/admin/order/delete",
            type: "POST",
            data: {id: $(obj).attr('data-order-id')},
            success: function(response) {
                var html = $.ajax({
                    url: "/admin/provider-order/get-detalization",
                    async: false,
                    type: "POST",
                    data: {date: $("#details-date").val()}
                }).responseText;
                if (html) {
                    $("#purchase-details-container").html(html);
                }
            }
        });
    }
}

function deleteReturnOrder(obj)
{
    if (confirm('Вы уверены, что хотите сделать возврат и удалить этот заказ?')) {
        $.ajax({
            url: "/admin/order/delete-return",
            type: "POST",
            data: {id: $(obj).attr('data-order-id')},
            success: function(response) {
                var html = $.ajax({
                    url: "/admin/provider-order/get-detalization",
                    async: false,
                    type: "POST",
                    data: {date: $("#details-date").val()}
                }).responseText;
                if (html) {
                    $("#purchase-details-container").html(html);
                }
            }
        });
    }
}

function deleteOrderStock(obj)
{
    if (confirm('Вы уверены, что хотите удалить этот заказ?')) {
        $.ajax({
            url: "/admin/order/delete",
            type: "POST",
            data: {id: $(obj).attr('data-order-id')},
            success: function(response) {
                var html = $.ajax({
                    url: "/admin/order/get-detalization",
                    async: false,
                    type: "POST",
                    data: {date_e: $("#details-date-end").val(), date_s: $("#details-date-start").val()}
                }).responseText;
                if (html) {
                    $("#purchase-details-container").html(html);
                }
            }
        });
    }
}

function deleteReturnOrderStock(obj)
{
    if (confirm('Вы уверены, что хотите сделать возврат и удалить этот заказ?')) {
        $.ajax({
            url: "/admin/order/delete-return",
            type: "POST",
            data: {id: $(obj).attr('data-order-id')},
            success: function(response) {
                var html = $.ajax({
                    url: "/admin/order/get-detalization",
                    async: false,
                    type: "POST",
                    data: {date_e: $("#details-date-end").val(), date_s: $("#details-date-start").val()}
                }).responseText;
                if (html) {
                    $("#purchase-details-container").html(html);
                }
            }
        });
    }
}

function deleteCheckedOrders()
{
    if (confirm('Вы уверены, что хотите удалить выбранные заказы?')) {
        $(".check_date").each(function() {
            if (this.checked) {
                var chk_date = $(this).attr("data-date");
                $.ajax({
                    url: "/admin/provider-order/delete",
                    type: "POST",
                    data: {date: chk_date},
                    async: false,
                    success: function(response) {
                        
                    }
                });
            }
            
        });
        window.location.reload();
    }
}

function deleteCheckedOrdersStock()
{
    if (confirm('Вы уверены, что хотите удалить выбранные заказы?')) {
        $(".check_date").each(function() {
            if (this.checked) {
                var chk_date = $(this).attr("data-date");
                $.ajax({
                    url: "/admin/order/delete-stock",
                    type: "POST",
                    data: {date: chk_date},
                    async: false,
                    success: function(response) {
                        
                    }
                });
            }
            
        });
        window.location.reload();
    }
}

function changeIsWeights(obj)
{
    if (obj.checked) {
        $("#weight-lbl").text("Масса/Объём 1 единицы");
        $("#count-lbl").text("Общий принимаемый вес");
        $("#summ-lbl").text("Сумма за 1 кг./л.");
    } else {
        $("#weight-lbl").text("Масса/Объём");
        $("#count-lbl").text("Количество");
        $("#summ-lbl").text("Сумма за ед./т.");
    }
}

function correctWeights(obj)
{
    $("#pname-td").html($(obj).attr("data-pname"));
    $("#price-td").html($(obj).attr("data-price"));
    $("#quantity-td").html($(obj).attr("data-quantity"));
    $("#total-td").html(parseFloat($(obj).attr("data-price") * $(obj).attr("data-quantity")).toFixed(2));
    $("#quantity-correct-txt").val($(obj).attr("data-quantity"));
    $("#ohp-id").val($(obj).attr("data-ohp-id"));
    $('#correct-weights-modal').modal('show');
}

function setTotalCorrect()
{
    $("#total-td").html(parseFloat($("#quantity-correct-txt").val() * $("#price-td").html()).toFixed(2));
}

function correctRecalc()
{
    var c_quantity = $("#quantity-correct-txt").val();
    var c_total = $("#total-td").html();
    var id = $("#ohp-id").val();
    var prev_quantity = $("#quantity-td").html();
    
    $.ajax({
        url: "/admin/order/set-corrected",
        type: "POST",
        data: {quantity: c_quantity, prev_quantity: prev_quantity, total: c_total, id: id},
        async: false,
        success: function(response) {
            window.location.reload();
        }
    });
}
