<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getNavUrls() : array{
        $navUrls = [['name' => 'Főoldal', 'url' => route('home')]];
        if(Auth::user()){
            array_push($navUrls, ['name' => 'Munkalapok', 'url' => route('worksheet.index')]);
        }
        return $navUrls;
    }

    function getUserUrls(){
        $userUrls = [["name" => 'Profilom', 'url' => route('user')]];
        if(Auth::user()->isAdmin()){
            array_push($userUrls, ['name' => 'Munkatárs felvétele', 'url' => '/register']);
        }
        $userUrls[] = ["name" => 'Kijelentkezés', 'url' => route('logout')];
        return $userUrls;
    }

    public function home()
    {
        if (Auth::user()) {
            return view('layouts.menu', ["navUrls" => $this->getNavUrls(), "tickets" => [], "userUrls" => $this->getUserUrls()]);
        } else {
            return view('layouts.menu', ["navUrls" => $this->getNavUrls()]);
        }
    }

    public function show(){
        return view('user.user', ["navUrls" => $this->getNavUrls(), "userUrls" => $this->getUserUrls()]);
    }

    public function update(Request $request){

    }
}
