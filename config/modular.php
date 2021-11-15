<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

return [
    'path' => base_path() . '/app/Modules', // полный путь к директории с модулями
    'base_namespace' => 'App\Modules', //базовое пространство имен для всех модулей
    'groupWithoutPrefix' => 'Pub', //указывается та родительская группа для котрой маршруты будут строится без родительского префикса
    'groupMidleware' => [
        'Admin' => [
            'web' => ['auth'], // закрываем все модули в родительском модуле от публичного доступа
            'api' => ['auth:api'], //
        ]
    ],
//в этом параметре в виде массива будут указаны все модули которые фремйворк должен будет обойти и считать маршруты кажого модуля
    'modules' => [
        'Admin' => [
            'Role',
            'Menu',
            'Dashboard',
            'User',
        ],
        'Pub' => [
            'Auth'
        ],
    ]
];
