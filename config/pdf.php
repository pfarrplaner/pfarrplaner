<?php

return [
    'mode' => 'utf-8',
    'format' => 'A4',
    'author' => '',
    'subject' => '',
    'keywords' => 'dienstplan,gottesdienste',
    'creator' => 'Online-Dienstplan des Distrikts Albstadt-Nord',
    'display_mode' => 'fullpage',
    'tempDir' => base_path('../temp/'),
    'font_path' => base_path('resources/fonts/'),
    'font_data' => [
        'helveticacondensed' => [
            'R' => 'HelveticaCondensed.ttf',
            'B' => 'HelveticaCdBd.ttf',
        ],
        'ptsans' => [
            'R' => 'PTSans-Regular.ttf',
            'I' => 'PTSans-Italic.ttf',
            'B' => 'PTSans-Bold.ttf',
            'BI' => 'PTSans-BoldItalic.ttf',
        ]
    ]
];
