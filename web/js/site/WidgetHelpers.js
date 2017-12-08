
var WidgetHelpers = {
    isSubmit: function (formData) {
        for (var count in formData) {
            if (formData[count]['name'] == 'submit') {
                return true;
            }
        }

        return false;
    },

    showFlashDialog: function (message, timeout) {
        if (typeof timeout === 'undefined') {
            timeout = 4000;
        }
        window.setTimeout(function () {
                yii.alert(message);
                window.setTimeout(function () {
                        $('.bootbox-alert').modal('hide');
                    },
                    timeout
                );
            },
            500
        );
    },

    showLoading: function () {
        $('body').showLoading();

        $('.loading-indicator-overlay').css('position', 'fixed');
        $('.loading-indicator-overlay').css('width', '100%');
        $('.loading-indicator-overlay').css('height', '100%');

        $('.loading-indicator').css('position', 'fixed');
        $('.loading-indicator').css('top', '48%');
        $('.loading-indicator').css('left', '48%');
    },

    hideLoading: function () {
        $('body').hideLoading();
    }
};
