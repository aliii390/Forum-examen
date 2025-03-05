<?php

namespace App\Interfaces;

use App\Entity\User;

interface UpdateProfileInterface
{
    public function updateProfile(User $user, string $name, string $email): void;
}


// string $plainPassword) rajoutez password apres pour l'instant je test juste avec le nom et l'email