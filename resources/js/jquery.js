import $ from 'jquery';
window.$ = window.jQuery = $;

import 'inputmask';

jQuery(function () {
    Inputmask({ "mask": "99999999999" }).mask("national_id");
    Inputmask({ "mask": "(999) 999-9999" }).mask("phone_number");
    Inputmask("email").mask("email");

    $("#phone_number").change(function () {
        console.log($("#phone_number").val() );
    })
});

