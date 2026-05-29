<?php

declare(strict_types=1);

namespace VigihdevWP\Repositories;

use WP_User;

final class UserRepository
{
    private ?WP_User $user;

    public function __construct() {}

    public function current()
    {
        return wp_get_current_user();
    }

    public function find(int $id): WP_User|false
    {
        return get_user_by('ID', $id);
    }
}
