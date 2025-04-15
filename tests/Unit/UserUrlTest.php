<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserUrlTest extends TestCase
{
    public function test_admin_user_gets_register_url()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $urls = $user->getUserUrls();
        $this->assertTrue(collect($urls)->contains(fn($url) => $url['url'] === '/register'));
    }

    public function test_regular_user_does_not_get_register_url()
    {
        $user = User::factory()->create(['role' => 'coworker']);
        $urls = $user->getUserUrls();
        $this->assertFalse(collect($urls)->contains(fn($url) => $url['url'] === '/register'));
    }

    public function test_user_does_not_get_search_url_default()
    {
        $user = User::factory()->create(['role' => 'liable']);
        $urls = $user->getUserUrls();
        $this->assertFalse(collect($urls)->contains(fn($url) => $url['name'] === 'search'));
    }

    public function test_user_gets_search_url_when_needed()
    {
        $user = User::factory()->create(['role' => 'liable']);
        $urls = $user->getUserUrls(true);
        $this->assertTrue(collect($urls)->contains(fn($url) => $url['name'] === 'search'));
    }

    public function test_nav_urls_for_admin()
    {
        $urls = User::getNavUrls('admin');
        $this->assertFalse(collect($urls)->contains(fn($url) => $url['name'] === 'Munkalapok'));
    }

    public function test_nav_urls_for_non_admin()
    {
        $urls = User::getNavUrls('coworker');
        $this->assertTrue(collect($urls)->contains(fn($url) => $url['name'] === 'Munkalapok'));
    }

    public function test_nav_urls_for_create()
    {
        $urls = User::getNavUrls('liable', [["type" => "create", "text" => "munkalap", "url" => route('worksheet.create')], ["type" => "create", "text" => "munkajegy", "url" => route('ticket.create')]]);
        $this->assertTrue(collect($urls)->contains(fn($url) => $url['name'] === 'Új munkalap'));
        $this->assertTrue(collect($urls)->contains(fn($url) => $url['name'] === 'Új munkajegy'));
    }

    public function test_nav_urls_include_home_for_guest()
    {
        $urls = User::getNavUrls(null);
        $this->assertEquals('Főoldal', $urls[0]['name']);
    }
}
