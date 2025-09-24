<?php

namespace App\Policies;

use Modules\User\Models\User;

class UserPolicy
{
    // /**
    //  * Determine if the user can access admin dashboard.
    //  */
    // public function accessDashboard(User $user): bool
    // {
    //     return $user->role === 'admin';
    // }

    // /**
    //  * Determine if the user can access website (customer area).
    //  */
    // public function accessWebsite(User $user): bool
    // {
    //     return $user->role === 'customer';
    // }

    // /**
    //  * Basic permission check - admin can do anything with users.
    //  */
    // public function manage(User $user): bool
    // {
    //     return $user->role === 'admin';
    // }

    // /**
    //  * Users can view their own profile.
    //  */
    // public function viewOwnProfile(User $user, User $model): bool
    // {
    //     return $user->id === $model->id;
    // }
}
