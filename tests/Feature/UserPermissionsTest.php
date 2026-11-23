<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_individual_permissions_are_persisted_and_returned(): void
    {
        User::create([
            'id' => 'u-permissions',
            'name' => 'Test Operator',
            'email' => 'operator@example.com',
            'role' => 'operator',
            'password' => 'password',
        ]);

        $permissions = ['home', 'shaft_operations', 'shaft_daily_report'];

        $this->putJson('/api/users/u-permissions', ['permissions' => $permissions])
            ->assertOk()
            ->assertJsonPath('permissions', $permissions);

        $this->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('0.permissions', $permissions);

        $this->assertDatabaseHas('users', ['id' => 'u-permissions']);
        $this->assertSame($permissions, User::findOrFail('u-permissions')->permissions);
    }
}
