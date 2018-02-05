$(document).ready(function() {
    if ($("#vote-active").val() == 1) {
        $("#user-menu-lnk a.dropdown-toggle").addClass('blink');
        $("#vote-menu-lnk a").addClass('blink1');
    }
    
    $('#update-mailing-btn').click(function () {
        $.ajax({
            url: "/mailing/settings",
            type: 'POST',
            data: $("#update-mailing-frm").serialize(),
            success: function (data) {
                if (!(data && data.success)) {
                    alert('Ошибка обновления настроек');
                }
                window.location.reload();
            },
            error: function () {
                alert('Ошибка обновления настроек');
            },
        });

        return false;
    });
})

function sendVote(obj)
{
    var vote_id = $(obj).attr("data-id");
    var vote_val = $("[name=vote-" + vote_id + "]:checked").val();
    var vote_label = $("[name=vote-" + vote_id + "]:checked").attr('data-label');
    
    var modalVoteConfirm = function(callback) {
        $("#vote-sending-modal").modal('show');
        $("#vote-subject").html($("#vote-subject-" + vote_id).val());
        $("#vote-result").html(vote_label);
        
        $("#vote-sending-btn").on("click", function(){
            callback(true);
            $("#vote-sending-modal").modal('hide');
        });
  
        $("#vote-sending-cancel-btn").on("click", function(){
            callback(false);
            $("#vote-sending-modal").modal('hide');
        });
    }
    modalVoteConfirm(function(confirm) {
        if (confirm) {
            $.ajax({
                url: "/mailing/vote",
                type: 'POST',
                data: {vote_id: vote_id, vote_val: vote_val},
                success: function (data) {
                    if (!(data && data.success)) {
                        alert('Ошибка отправки голоса');
                    }
                    $("#vote-sending-thanx-modal").modal('show');
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                    
                },
                error: function () {
                    alert('Ошибка отправки голоса');
                },
            });
        }
    });
}

function sendMailingMessage()
{
    $.ajax({
        url: "/mailing/message",
        type: 'POST',
        data: $("#mailing_message_frm").serialize(),
        success: function (data) {
            if (!(data && data.success)) {
                alert('Ошибка отправки голоса');
            }
            $("#message-sending-thanx-modal").modal('show');
            setTimeout(function() {
                window.location.reload();
            }, 2000);
            
        },
        error: function () {
            alert('Ошибка отправки голоса');
        },
    });
}