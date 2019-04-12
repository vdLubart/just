<?php

namespace Lubart\Just\Tests\Feature\Just\Users;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Just\Models\User;

class Actions extends TestCase{
    
    use WithFaker;
    
    public function tearDown(){
        foreach(User::all() as $user){
            if($user->id > 2){
                $user->delete();
            }
            else{
                $user->password = bcrypt($user->role);
                $user->save();
            }
        }
        
        parent::tearDown();
    }
    
    public function see_user_list($assertion){
        $response = $this->get('admin/settings/user/list');
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: Users");
        }
        else{
            if(\Auth::id()){
                $this->followRedirects($response)->assertSee("You do not have permitions for that action");
            }
            else{
                $response->assertRedirect('/login');
            }
        }
    }
    
    public function create_new_admin($assertion){
        $response = $this->get('admin/settings/user/0');
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: User");
        }
        else{
            if(\Auth::id()){
                $this->followRedirects($response)->assertSee("You do not have permitions for that action");
            }
            else{
                $response->assertRedirect('/login');
            }
        }
        
        $this->post('admin/settings/user/setup', [
            "user_id" => null,
            "email" => $email = $this->faker->email,
            "name" => $name = $this->faker->name,
            "role" => "admin", 
            "password" => $password = $this->faker->word,
            "password_confirmation" => $password,
        ]);
        
        $user = User::where('email', $email)->first();
        
        if($assertion){
            $this->assertNotNull($user);
            
            $this->assertEquals($name, $user->name);
        }
        else{
            $this->assertNull($user);
        }
    }
    
    public function create_new_master($assertion){
        $response = $this->get('admin/settings/user/0');
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: User");
        }
        else{
            if(\Auth::id()){
                $this->followRedirects($response)->assertSee("You do not have permitions for that action");
            }
            else{
                $response->assertRedirect('/login');
            }
        }
        
        $this->post('admin/settings/user/setup', [
            "user_id" => null,
            "email" => $email = $this->faker->email,
            "name" => $name = $this->faker->name,
            "role" => "master", 
            "password" => $password = $this->faker->word,
            "password_confirmation" => $password,
        ]);
        
        $user = User::where('email', $email)->first();
        
        if($assertion){
            $this->assertNotNull($user);
            
            $this->assertEquals($name, $user->name);
        }
        else{
            $this->assertNull($user);
        }
    }
    
    public function edit_user_email($assertion){
        $user = User::create([
            "email" => $this->faker->email,
            "name" => $this->faker->name,
            "role" => "master",
            "password" => bcrypt($this->faker->word),
        ]);
        
        $response = $this->get('admin/settings/user/'.$user->id);
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: User");
        }
        else{
            if(\Auth::id()){
                $this->followRedirects($response)->assertSee("You do not have permitions for that action");
            }
            else{
                $response->assertRedirect('/login');
            }
        }
        
        $this->post('admin/settings/user/setup', [
            "user_id" => $user->id,
            "email" => $email = $this->faker->email,
            "name" => $name = $this->faker->name,
            "role" => "master"
        ]);
        
        $user = User::where('email', $email)->first();
        
        if($assertion){
            $this->assertNotNull($user);
            
            $this->assertEquals($name, $user->name);
        }
        else{
            $this->assertNull($user);
        }
    }
    
    public function change_own_password($assertion){
        $response = $this->get('admin/settings/password');
        
        if($assertion){
            $response->assertSuccessful()
                    ->assertSee("Settings :: User :: Change Password");
            
            $user = \Auth::user();
            
            $this->post('admin/settings/password/update', [
                "current_password" => $user->role, // password is same as role name
                "new_password" => $newPass = $this->faker->word,
                "new_password_confirmation" => $newPass
            ]);
            
            $this->post('login', [
                "email" => $user->email,
                "password" => $newPass
            ]);
            
            $this->assertTrue(\Auth::check());
            $this->assertEquals($user->email, \Auth::user()->email);
            
            $user->password = $user->role;
            $user->save();
        }
        else{
            $response->assertRedirect('/login');
        }
    }
    
    public function cannot_change_own_password_without_current_one(){
        $response = $this->get('admin/settings/password');
        
        $response->assertSuccessful()
                ->assertSee("Settings :: User :: Change Password");

        $user = \Auth::user();

        $response = $this->post('admin/settings/password/update', [
            "current_password" => 'wrong',
            "new_password" => $newPass = $this->faker->word,
            "new_password_confirmation" => $newPass
        ]);
        
        $this->followRedirects($response)->assertSee('Current password is incorrect');
    }


    public function delete_user($assertion){
        $user = User::create([
            "email" => $this->faker->email,
            "name" => $this->faker->name,
            "role" => "master",
            "password" => bcrypt($this->faker->word),
        ]);
        
        $this->post('admin/user/delete', [
            'id' => $user->id
        ]);
        
        $deletedUser = User::find($user->id);
        
        if($assertion){
            $this->assertNull($deletedUser);
        }
        else{
            $this->assertNotNull($deletedUser);
        }
    }
    
    public function delete_yourself($assertion){
        $this->post('admin/user/delete', [
            'id' => \Auth::id()
        ]);
        
        $deletedUser = User::find(\Auth::id());
        
        if($assertion){
            $this->assertNull($deletedUser);
        }
        else{
            $this->assertNotNull($deletedUser);
        }
    }
}
