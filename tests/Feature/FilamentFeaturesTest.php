<?php

namespace Tests\Feature;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Models\User;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\Email\Contracts\HasEmailAuthentication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FilamentFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_supports_google_and_email_two_factor_authentication(): void
    {
        $user = User::factory()->create(['type' => 'admin']);

        $this->assertInstanceOf(HasAppAuthentication::class, $user);
        $this->assertInstanceOf(HasEmailAuthentication::class, $user);

        $user->saveAppAuthenticationSecret('google-secret');
        $user->toggleEmailAuthentication(true);
        $user->refresh();

        $this->assertSame('google-secret', $user->getAppAuthenticationSecret());
        $this->assertTrue($user->hasEmailAuthentication());
        $this->assertNotSame(
            'google-secret',
            DB::table('users')->where('id', $user->getKey())->value('app_authentication_secret'),
        );
    }

    public function test_creating_a_user_sends_a_database_notification(): void
    {
        $recipient = User::factory()->create(['type' => 'admin']);
        $notificationsBefore = $recipient->notifications()->count();

        User::factory()->create();

        $this->assertSame($notificationsBefore + 1, $recipient->notifications()->count());
        $this->assertSame('User Created', $recipient->notifications()->latest()->first()->data['title']);
    }

    public function test_user_import_and_export_columns_match_the_course(): void
    {
        $importColumns = collect(UserImporter::getColumns())
            ->map(fn ($column): string => $column->getName())
            ->all();
        $exportColumns = collect(UserExporter::getColumns())
            ->map(fn ($column): string => $column->getName())
            ->all();

        $this->assertSame(['name', 'email', 'password'], $importColumns);
        $this->assertSame(['id', 'name', 'email', 'created_at'], $exportColumns);
    }
}
