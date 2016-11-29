<?php

return array(

    // 'proAndroid' => array(
    //     'environment' =>'production',
    //     'apiKey'      =>'yourAPIKey',
    //     'service'     =>'gcm'
    // ),
    'proIOS'     => array(
        'environment' =>'production',
        'certificate' =>storage_path().'/mimi_push_cert_prod.pem',
        'passPhrase'  =>'',
        'service'     =>'apns'
    ),
    'devIOS'     => array(
        'environment' =>'development',
        'certificate' =>storage_path().'/mimi_push_cert_dev.pem',
        'passPhrase'  =>'',
        'service'     =>'apns'
    ),
    'devAndroid' => array(
        'environment' =>'development',
        'apiKey'      =>'AIzaSyA9SNdZmbvYdKb5GH47yfKl2nhfjTB_ES4',
        'service'     =>'gcm'
    )



);