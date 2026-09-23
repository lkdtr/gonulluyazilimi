<?php

return [

    // Mailgun list that receives announcements marked "send e-mail", and its test list.
    'mailing_list' => env('ANNOUNCEMENTS_MAILING_LIST', 'gonullu@mg.penguen.org.tr'),
    'test_mailing_list' => env('ANNOUNCEMENTS_TEST_MAILING_LIST', 'gonullu-test@mg.penguen.org.tr'),

];
