
var CartHelpers = {
    Urls: {
        Add: '/api/cart/default/add',
        Update: '/api/cart/default/update',
        Clear: '/api/cart/default/clear'
    },
    Information: '',
    UpdatedProductQuantity: '',
    UpdatedProductInformation: '',
    Order: false,
    Message: '',

    add: function (id, quantity) {
        var result = false;

        $.ajax({
            url: CartHelpers.Urls.Add,
            type: 'POST',
            async: false,
            cache: false,
            timeout: 30000,
            data: {
                ProductAddition: {
                    id: id,
                    quantity: quantity
                }
            },
            success: function (data) {
                CartHelpers.Order = result = data.success;
                CartHelpers.Message = data.message;
                CartHelpers.Information = data.cartInformation;
                CartHelpers.UpdatedProductQuantity = data.productQuantity;
                CartHelpers.UpdatedProductInformation = data.productInformation;
                CartHelpers.Order = data.order;
            },
            error: function () {
                result = false;
                CartHelpers.Order = result;
                CartHelpers.Message = 'Произошла ошибка при добавлении товара!';
                CartHelpers.Information = '';
                CartHelpers.UpdatedProductQuantity = '';
                CartHelpers.UpdatedProductInformation = '';
            },
        });

        return result;
    },

    update: function (id, quantity) {
        var result = false;

        $.ajax({
            url: CartHelpers.Urls.Update,
            type: 'POST',
            async: false,
            cache: false,
            timeout: 30000,
            data: {
                ProductUpdating: {
                    id: id,
                    quantity: quantity
                }
            },
            success: function (data) {
                result = data.success;
                CartHelpers.Message = data.message;
                CartHelpers.Information = data.cartInformation;
                CartHelpers.UpdatedProductQuantity = data.productQuantity;
                CartHelpers.UpdatedProductInformation = data.productInformation;
                CartHelpers.Order = data.order;
            },
            error: function () {
                result = false;
                CartHelpers.Order = result;
                CartHelpers.Message = 'Произошла ошибка при обновлении товара!';
                CartHelpers.Information = '';
                CartHelpers.UpdatedProductQuantity = '';
                CartHelpers.UpdatedProductInformation = '';
            },
        });

        return result;
    },

    clear: function () {
        var result = false;

        CartHelpers.Order = false;
        CartHelpers.Information = '';
        CartHelpers.UpdatedProductQuantity = '';
        CartHelpers.UpdatedProductInformation = '';

        $.ajax({
            url: CartHelpers.Urls.Clear,
            type: 'POST',
            async: false,
            cache: false,
            timeout: 30000,
            success: function (data) {
                result = data.success;
                CartHelpers.Message = data.message;
            },
            error: function () {
                result = false;
                CartHelpers.Message = 'Произошла ошибка при очистки корзины!';
            },
        });

        return result;
    }
};
