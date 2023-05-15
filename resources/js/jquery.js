import $ from 'jquery';
window.$ = window.jQuery = $;

import 'inputmask';
import 'jquery-datetimepicker';

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

    $("#phone_number").on("change", function () {

        $("#phone_number_validation_block").show();
        $("#hidden_phone_number").val($("#phone_number").val());

        var phone_number = $("#hidden_phone_number").val();

        $.ajax({
            url: "/phone-number-verification-request",
            data: "phone_number=" + phone_number + "&_token=" + _globalToken._token,
            type: 'POST',
            success: function (data) {

                if (data.status == true) {

                    $("#phone_number").attr("readonly", "readonly");
                    $("#label_phone_number").show();
                    $("#phone_number").hide();

                }
                else {
                    alert(data.message);
                }
            },
            error: function (data) {
                alert("API yanıt vermiyor.");
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
                }
                else {
                    alert(data.message);
                }
            },
            error: function (data) {
                alert("API yanıt vermiyor.");
                return false;
            }
        });


    });

});
