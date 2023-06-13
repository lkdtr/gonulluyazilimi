import $ from 'jquery';
window.$ = window.jQuery = $;

import 'inputmask';
import 'jquery-datetimepicker';
import 'datatables.net-bs4';

jQuery(function () {
    Inputmask({ "mask": "99999999999" }).mask("national_id");
    Inputmask({ "mask": "(599) 999-9999" }).mask("phone_number");
    Inputmask("email").mask("email");

    jQuery.datetimepicker.setLocale('tr');

    jQuery('#birthday').datetimepicker({
        timepicker: false,
        format: 'd-m-Y',
        lang: 'tr',
        mask: true,
        dayOfWeekStart: 1
    });

    $('table').dataTable();

    function passwordChecker(input, alertbox) {

        $(input).on('keyup', function () {
            var number = /([0-9])/;
            var alphabets = /([a-zA-Z])/;
            var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
            if ($(input).val().length < 8) {
                $(alertbox).removeClass();
                $(alertbox).addClass('weak-password');
                $(alertbox).html("Basit (en az 8 karakter olmalıdır.)");
            } else {
                if ($(input).val().match(number) && $(input).val().match(alphabets) && $(input).val().match(special_characters)) {
                    $(alertbox).removeClass();
                    $(alertbox).addClass('strong-password');
                    $(alertbox).html("Güçlü");
                } else {
                    $(alertbox).removeClass();
                    $(alertbox).addClass('medium-password');
                    $(alertbox).html("Orta (harfleri, sayıları ve özel karakterleri veya bazı kombinasyonları içermelidir.)");
                }
            }

            if (($(input).attr('id') == "password-confirm") && ($("#password").val() != $("#password-confirm").val() ) ) {
                $(alertbox).removeClass();
                $(alertbox).addClass('notmatch-password');
                $(alertbox).html("Parolalar uyuşmuyor");
            }

            $(alertbox).addClass('password-strength-status');
        });

    }

    if($("#password").length>0) {
        passwordChecker($("#password"), $("#password-strength-status"));
    }
    if ($("#password").length > 0) {
        passwordChecker($("#password-confirm"), $("#password-confirm-strength-status"));
    }

    $("#phone_number").on("change", function () {

        $("#hidden_phone_number").val($("#phone_number").val());

        var phone_number = $("#hidden_phone_number").val();

        if (phone_number.length < 6) {
            return false;
        }

        $("#phone_number_validation_block").show();

        $.ajax({
            url: "/phone-number-verification-request",
            data: "phone_number=" + phone_number + "&_token=" + _globalToken._token,
            type: 'POST',
            success: function (data) {

                if (data.status == true) {

                    $("#phone_number").attr("readonly", "readonly");
                    $("#label_phone_number").show();
                    $("#phone_number").hide();
                    $("#phone_number_validation").focus();

                }
                else {
                    $("#register_button").prop("disabled", true);
                    alert(data.message);
                }
            },
            error: function (data) {
                if(data.responseJSON) {
                    alert(data.responseJSON.message);
                }
                else {
                    alert("API yanıt vermiyor.");
                }
                return false;
            }
        });


    });

    $("#validate_button").on("click", function () {

        var phone_number = $("#hidden_phone_number").val();
        var validation = $("#phone_number_validation").val();

        $.ajax({
            url: "/phone-number-verification",
            data: "phone_number=" + phone_number + "&validation=" + validation + "&_token=" + _globalToken._token,
            type: 'POST',
            success: function (data) {

                if (data.status == true) {

                    $("#phone_number_validation_block").hide();

                    $("#phone_number").attr("readonly", "readonly");
                    $("#label_phone_number").hide();
                    $("#phone_number").show();

                    $("#register_button").prop("disabled", false);
                    $("#info-block").hide();

                    $("#register_form").after('<input type="hidden" name="phone_number_verified" value="' + _globalToken._token + '">');
                }
                else {
                    $("#register_button").prop("disabled", true);
                    alert(data.message);
                }
            },
            error: function (data) {
                if (data.responseJSON) {
                    alert(data.responseJSON.message);
                }
                else {
                    alert("API yanıt vermiyor.");
                }
                return false;
            }
        });


    });

});
