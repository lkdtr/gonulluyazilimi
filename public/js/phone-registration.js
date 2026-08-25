document.addEventListener('DOMContentLoaded', function () {
    var phoneInput = $('#phone_number');
    var requestButton = $('#phone_number_request');

    if (!phoneInput.length || !requestButton.length) return;

    phoneInput.off('change');
    if (phoneInput.inputmask) phoneInput.inputmask('remove');

    requestButton.on('click', function () {
        var phoneNumber = phoneInput.val().replace(/\D/g, '');

        if (phoneNumber.length === 11 && phoneNumber.startsWith('0')) {
            phoneNumber = '90' + phoneNumber.substring(1);
        } else if (phoneNumber.length === 10 && phoneNumber.startsWith('5')) {
            phoneNumber = '90' + phoneNumber;
        }

        if (phoneNumber.length < 10 || phoneNumber.length > 15) {
            alert('Lütfen geçerli bir telefon numarası girin.');
            return;
        }

        $('#hidden_phone_number').val(phoneNumber);
        $('#phone_number_validation_block').show();
        requestButton.prop('disabled', true);

        $.post('/phone-number-verification-request', {
            phone_number: phoneNumber,
            _token: _globalToken._token
        }).done(function (data) {
            if (data.status) {
                phoneInput.prop('readonly', true);
                requestButton.hide();
                $('#label_phone_number').show();
                phoneInput.hide();
                $('#phone_number_validation').focus();
            } else {
                requestButton.prop('disabled', false);
                alert(data.message);
            }
        }).fail(function (response) {
            requestButton.prop('disabled', false);
            alert(response.responseJSON ? response.responseJSON.message : 'API yanıt vermiyor.');
        });
    });
});
