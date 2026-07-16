<?php

namespace App\Observers;

use App\Models\User;
use Filament\Notifications\Notification;

class UserObserver
{
    public function created(User $user): void
    {
        Notification::make()
            ->title('User Created')
            ->body("{$user->name} has been created.")
            ->sendToDatabase(User::query()->first());
    }
}
