function detalization()
{
    if ($("#purchase-details-btn").hasClass("closed")) {
        var html = $.ajax({
            url: "/profile/provider/order/get-detalization",
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
        $("#purchase-details-container").slideUp();
        $.ajax({
            url: "/profile/provider/order/show-all",
            type: "POST",
            data: {date: $("#details-date").val()},
            success: function(response) {
                
            }
        });
    }
}

function setPageView()
{
    var html = $.ajax({
        url: "/profile/provider/order/set-view",
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

function hideOrder(obj)
{
    var html = $.ajax({
        url: "/profile/provider/order/hide",
        async: false,
        type: "POST",
        data: {o_id: $(obj).attr("data-order-id"), date: $(obj).attr("data-date")}
    }).responseText;
    if (html) {
        $("#purchase-details-container").html(html);
    }
}