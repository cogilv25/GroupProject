<?php

declare(strict_types=1);

use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\Container;

return function (Container $container) {
    // Here we map our UserRepository interface to its in memory implementation
    $container->set(UserRepository::class, \DI\autowire(InMemoryUserRepository::class));
};
