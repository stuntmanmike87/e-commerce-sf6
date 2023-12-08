<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../src/Controller/', 'attribute');//'annotation');

    $routingConfigurator->import('../src/Kernel.php', 'attribute');//'annotation');
};
