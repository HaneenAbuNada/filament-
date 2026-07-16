<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_user_type_can_only_access_its_own_panel(): void
    {
        foreach ([
            'admin' => '/admin',
            'manager' => '/manager',
            'user' => '/user',
        ] as $type => $allowedPanel) {
            $user = User::factory()->create(['type' => $type]);

            $this->actingAs($user)->get($allowedPanel)->assertOk();

            foreach (array_diff(['/admin', '/manager', '/user'], [$allowedPanel]) as $forbiddenPanel) {
                $this->actingAs($user)->get($forbiddenPanel)->assertForbidden();
            }

            auth()->logout();
        }
    }

    public function test_product_and_user_policies_follow_the_course_roles(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $manager = User::factory()->create(['type' => 'manager']);
        $user = User::factory()->create(['type' => 'user']);

        $this->assertTrue($admin->can('create', User::class));
        $this->assertFalse($manager->can('create', User::class));
        $this->assertTrue($manager->can('viewAny', User::class));
        $this->assertFalse($user->can('viewAny', User::class));

        $this->assertTrue($admin->can('create', Product::class));
        $this->assertTrue($manager->can('create', Product::class));
        $this->assertTrue($user->can('create', Product::class));
    }
}
