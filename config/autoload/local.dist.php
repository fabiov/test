<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return [
    'db' => [
        'driver'         => 'Pdo',
        'driver_options' => [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''],
        'dsn'            => 'mysql:dbname=easywallet_dev;host=localhost',
        'password'       => 'root',
        'username'       => 'root',
    ],
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => [
                    'dbname'   => 'easywallet_dev',
                    'host'     => 'localhost',
                    'password' => 'root',
                    'port'     => 3306,
                    'user'     => 'root',
                ]
            ]
        ]
    ],
    'mail' => [
        'transport' => [
            'options' => [

                // configurazione STMP gmail con tls
                'host'              => 'smtp.gmail.com',
                'connection_class'  => 'plain',
                'connection_config' => ['password' => '', 'ssl' => 'tls', 'username' => 'xxx@gmail.com'],

                // configurazione STMP register con ssl
//                'connection_class'  => 'login',
//                'connection_config' => ['password' => 'xxx', 'ssl' => 'ssl', 'username' => 'catchall@venol.it'],
//                'host'              => 'authsmtp.securemail.pro',
//                'name'              => 'authsmtp.securemail.pro',
//                'port'              => 465,
            ],
        ],
    ],
];