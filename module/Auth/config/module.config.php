<?php

declare(strict_types=1);

use Auth\Controller\RegistrationController;
use Auth\Controller\UserController;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceManager;

return [
    'controllers' => [
        'factories' => [
            UserController::class => function (ServiceManager $controllerManager) {
                return new UserController(
                    $controllerManager->get(AuthenticationService::class)->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get(Auth\Service\AuthManager::class)
                );
            },
            RegistrationController::class => function (ServiceManager $controllerManager) {
                return new RegistrationController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager
                );
            },
        ],
    ],
    'router' => [
        'routes' => [
            'auth' => [
                'type' => 'segment',
                'options' => [
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]+', 'id' => '[\w]+'],
                    'defaults' => ['controller' => UserController::class, 'action' => 'index'],
                    'route' => '/auth/user[/:action][/:id]',
                ],
            ],
            'auth_registration' => [
                'type' => 'segment',
                'options' => [
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]+', 'id' => '[\w]+'],
                    'defaults' => ['controller' => RegistrationController::class, 'action' => 'index'],
                    'route' => '/auth/registration[/:action][/:id]',
                ],
            ],
        ],
    ],
    'service_manager' => [
        // added for Authentication and Authorization. Without this each time we have to create a new instance.
        // This code should be moved to a module to allow Doctrine to overwrite it
        'aliases' => [],
        'factories' => [
            Auth\Service\AuthAdapter::class => function (ContainerInterface $controllerManager) {
                return new Auth\Service\AuthAdapter($controllerManager->get('doctrine.entitymanager.orm_default'));
            },
            Auth\Service\AuthManager::class => function (ContainerInterface $container) {
                return new Auth\Service\AuthManager($container->get(AuthenticationService::class));
            },
            AuthenticationService::class => function (ContainerInterface $container): AuthenticationService {
                return new AuthenticationService(
                    new Laminas\Authentication\Storage\Session(),
                    $container->get(Auth\Service\AuthAdapter::class)
                );
            },
        ],
        'invokables' => [
        ],
    ],
    'view_manager' => [
        'display_exceptions' => true,
        'template_path_stack' => [
            'auth' => __DIR__ . '/../view',
        ],
    ],
];
