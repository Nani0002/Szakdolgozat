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

    public function test_layouts_menu_view_receives_correct_nav_urls_based_on_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get(route('home'));
        $response->assertViewHas('userUrls', function ($urls) {
            return collect($urls)->contains(fn($url) => str_contains($url['url'], 'register'));
        });

        $liable = User::factory()->create(['role' => 'liable']);
        $this->actingAs($liable);
        $response = $this->get(route('home'));
        $response->assertViewHas('navUrls', function ($urls) {
            return collect($urls)->contains(fn($url) => str_contains($url['url'], 'worksheet'));
        });

        $coworker = User::factory()->create(['role' => 'coworker']);
        $this->actingAs($coworker);
        $response = $this->get(route('home'));
        $response->assertViewHas('navUrls', function ($urls) {
            return collect($urls)->contains(fn($url) => str_contains($url['url'], 'ticket'));
        });
    }
}
