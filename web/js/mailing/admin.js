$(document).ready(function() {
    $("[name=category]").click(function() {
        if ($(this).val() == '5') {
            $("#candidates").prop('disabled', true);
            $("#candidates").prop('checked', false);
            $("#candidates-groups").hide();
            $("#message-container").hide();
            $("#subject-container").hide();
            $("#subject-vote-container").show();
        } else {
            $("#candidates").prop('disabled', false);
            $("#message-container").show();
            $("#subject-container").show();
            $("#subject-vote-container").hide();
        }
    })
    
    $("#candidates").change(function() {
        if (this.checked) {
            $("#candidates-groups").show();
            $("#candidates-all").prop('checked', true);
            $("#candidates-all").prop('disabled', true);
            $("#candidates-all-hdn").val('1');
            $(".candidates-gr").prop('checked', false);
        } else {
            $("#candidates-groups").hide();
            $("#candidates-all").prop('checked', false);
            $("#candidates-all").prop('disabled', false);
            $("#candidates-all-hdn").val('0');
            $(".candidates-gr").prop('checked', false);
        }
    });
    
    $(".candidates-gr").change(function() {
        if (this.checked) {
            $("#candidates-all").prop('checked', false);
            $("#candidates-all").prop('disabled', false);
            $("#candidates-all-hdn").val('0');
        } else {
            var unchecked_cnt = 0;
            $(".candidates-gr").each(function(index, element) {
                if (element.checked === false) {
                    unchecked_cnt ++;
                }
            });
            if (unchecked_cnt == $(".candidates-gr").length) {
                $("#candidates-all").prop('checked', true);
                $("#candidates-all").prop('disabled', true);
                $("#candidates-all-hdn").val('1');
            }
        }
    });
    
    $("#candidates-all").change(function() {
        $(".candidates-gr").prop('checked', false);
        $("#candidates-all").prop('disabled', true);
        $("#candidates-all-hdn").val('1');
    });
    
    var modalConfirm = function(callback) {
        $("#send-mailing-btn").on("click", function() {
            if ($("#members").prop('checked') === true) {
                $("#members-modal").show();
            } else {
                $("#members-modal").hide();
            }
            if ($("#providers").prop('checked') === true) {
                $("#providers-modal").show();
            } else {
                $("#providers-modal").hide();
            }
            if ($("#candidates").prop('checked') === true) {
                $("#candidates-modal").show();
            } else {
                $("#candidates-modal").hide();
            }
            if ($("#members").prop('checked') === false && $("#providers").prop('checked') === false && $("#candidates").prop('checked') === false) {
                $("#modal-title-empty-address").show();
                $("#modal-title-ok").hide();
                $("#mailing-info-btn").hide();
            } else {
                $("#modal-title-empty-address").hide();
                $("#modal-title-ok").show();
                $("#mailing-info-btn").show();
            }
            
            
            if ($("[name=category]:checked").val() != 5) {
                if ($("#message").val() == "") {
                    $("#modal-title-empty-message").show();
                    $("#mailing-info-btn").hide();
                } else {
                    $("#modal-title-empty-message").hide();
                    $("#mailing-info-btn").show();
                }
                if ($("#subject").val() == "") {
                    $("#modal-title-empty-subject").show();
                    $("#mailing-info-btn").hide();
                } else {
                    $("#modal-title-empty-subject").hide();
                    $("#mailing-info-btn").show();
                }
            } else {
                if ($("#subject-vote").val() == "") {
                    $("#modal-title-empty-subject").show();
                    $("#mailing-info-btn").hide();
                } else {
                    $("#modal-title-empty-subject").hide();
                    $("#mailing-info-btn").show();
                }
            }
            
            $("#send-mailing-modal").modal('show');
        });

        $("#mailing-info-btn").on("click", function(){
            callback(true);
            $("#send-mailing-modal").modal('hide');
        });
  
        $("#mailing-info-cancel-btn").on("click", function(){
            callback(false);
            $("#send-mailing-modal").modal('hide');
        });
    };

    modalConfirm(function(confirm) {
        if (confirm) {
            $("body").addClass("loading");
            var form_d = document.forms.mailing_info_frm,
                formData = new FormData(form_d),
                xhr = new XMLHttpRequest();

            xhr.open("POST", "/admin/mailing");

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        window.location.reload();
                    }
                }
            };

            xhr.send(formData);
        }
    });
    
    $("#mailing-product-info-save-btn").click(function() {
        if (confirm('Вы уверены, что желаете сохранить рассылку?')) {
            $.ajax({
                url: "/admin/mailing/product",
                type: 'POST',
                data: $("#mailing_product_info_frm").serialize(),
                success: function (data) {
                    if (!(data && data.success)) {
                        alert('Ошибка сохранения настроек');
                    } else {
                        window.location.reload();
                    }
                },
                error: function () {
                    alert('Ошибка сохранения настроек');
                },
            });
        }
    });
    
    $("#answer-message-btn").click(function() {
        $("#answer-message-container").show();
    });
    
    $("#answer-message-send-btn").click(function() {
        $.ajax({
            url: "/admin/mailing/message",
            type: 'POST',
            data: {subject: $("#answer-subject").val(), message: $("#answer-message").val(), user_id: $("#answer-message-user-id").val(), id: $("#answer-message-id").val()},
            success: function (data) {
                if (!(data && data.success)) {
                    alert('Ошибка сохранения настроек');
                }
                window.location = "/admin/mailing/message";
            },
            error: function () {
                alert('Ошибка сохранения настроек');
            },
        });
    });
});