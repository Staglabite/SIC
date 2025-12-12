<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    */

    'show_warnings' => false,   // Set false untuk production
    
    'public_path' => null,

    /*
    |--------------------------------------------------------------------------
    | DOMPDF Options
    |--------------------------------------------------------------------------
    | Available options and their defaults: https://github.com/dompdf/dompdf#user-content-dompdf-options
    */

    'options' => array(
        'font_dir' => storage_path('fonts/'),
        'font_cache' => storage_path('fonts/'),
        'temp_dir' => storage_path('app/'),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            "file://" => ["rules" => []],
            "http://" => ["rules" => []],
            "https://" => ["rules" => []]
        ],
        'log_output_file' => null,
        'defaultFont' => 'serif',
        'isRemoteEnabled' => false,  // Set false untuk keamanan
        'isHtml5ParserEnabled' => true,
        'isPhpEnabled' => true,
        'isFontSubsettingEnabled' => true,
        'debugPng' => false,
        'debugKeepTemp' => false,
        'debugCss' => false,
        'debugLayout' => false,
        'debugLayoutLines' => true,
        'debugLayoutBlocks' => true,
        'debugLayoutInline' => true,
        'debugLayoutPaddingBox' => true,
        'pdfBackend' => "CPDF",
        'defaultMediaType' => "screen",
        'defaultPaperSize' => "a4",
        'defaultPaperOrientation' => "portrait",
        'dpi' => 96,
        'fontHeightRatio' => 1.1,
        'isJavascriptEnabled' => false,
        'isRemoteEnabled' => false,
        'isFontSubsettingEnabled' => true,
        'debug' => false,
        'enable_unicode' => true,
    ),
);