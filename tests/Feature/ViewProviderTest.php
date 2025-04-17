<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_layouts_menu_view_receives_nav_variables_for_authenticated_user()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertViewHas('userUrls');
        $response->assertViewHas('navUrls');
    }

    public function test_layouts_menu_view_receives_navurls_for_guests()
    {
        $response = $this->get(route('home'));

        $response->assertViewHas('navUrls');
    }
}
