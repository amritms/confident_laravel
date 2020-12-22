<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\UsersController;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase, AdditionalAssertions;

    /** @test */
    public function update_saves_data_and_redirects_to_dashboard()
    {
        $name = $this->faker->name;
        $password = $this->faker->password(8);
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->put('/users', [
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');

        $user->refresh();
        $this->assertEquals($name, $user->name);
        $this->assertTrue(\Hash::check($password, $user->password));
    }

    /**
     * This test uses assertions from jason mccreary's test assertions package
     * This test is replacement for multiple form validations.
     * @test
     */
    public function update_uses_valiadation()
    {
        $this->assertActionUsesFormRequest(
            UsersController::class,
            'update',
            UserUpdateRequest::class
        );
    }
//    /** @test */
//    public function update_fails_for_invalid_name()
//    {
//        $name = null;
//        $password = $this->faker->password(8);
//        $user = factory(User::class)->create();
//
//        $response = $this->actingAs($user)
//            ->from(route('user.edit'))
//            ->put('/users', [
//                'name' => $name,
//                'password' => $password,
//                'password_confirmation' => $password
//        ]);
//
//        $response->assertRedirect(route('user.edit'));
//        $response->assertSessionHasErrors('name');
//    }
//
//    /** @test */
//    public function update_fails_for_invalid_password()
//    {
//        $name = $this->faker->name;
//        $password = null;
//        $user = factory(User::class)->create();
//
//        $response = $this->actingAs($user)
//            ->from(route('user.edit'))
//            ->put('/users', [
//                'name' => $name,
//                'password' => $password,
//                'password_confirmation' => $password
//            ]);
//
//        $response->assertRedirect(route('user.edit'));
//        $response->assertSessionHasErrors('password');
//    }
}
