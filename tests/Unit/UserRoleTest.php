<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_admin_role_is_admin(): void
    {
        $adminUser = User::factory()->create([
            "role" => "admin"
        ]);

        $this->assertTrue($adminUser->isAdmin());
    }

    public function test_user_with_liable_role_is_liable(): void
    {
        $liablenUser = User::factory()->create([
            "role" => "liable"
        ]);

        $this->assertTrue($liablenUser->isLiable());
    }

    public function test_user_with_coworker_role_is_coworker(): void
    {
        $coworkerUser = User::factory()->create([
            "role" => "coworker"
        ]);

        $this->assertTrue($coworkerUser->isCoworker());
    }
}
