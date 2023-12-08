<?php

declare(strict_types=1);

use App\Entity\Users;
use App\Security\UsersAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        //'enable_authenticator_manager' => true,
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
            Users::class => [
                'algorithm' => 'auto',
            ],
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => Users::class,
                    'property' => 'email',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'app_user_provider',
                'custom_authenticator' => UsersAuthenticator::class,
                'logout' => [
                    'path' => 'app_logout',
                ],
            ],
        ],
        'access_control' => [
            [
                'path' => '^/admin',
                'roles' => 'ROLE_PRODUCT_ADMIN',
            ],
            [
                'path' => '^/profil',
                'roles' => 'ROLE_USER',
            ],
        ],
        'role_hierarchy' => [
            'ROLE_PRODUCT_ADMIN' => 'ROLE_USER',
            'ROLE_ADMIN' => 'ROLE_PRODUCT_ADMIN',
            'ROLE_SUPER_ADMIN' => 'ROLE_ADMIN',
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
