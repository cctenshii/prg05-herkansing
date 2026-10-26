<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role_id' => \App\Models\Role::where('name', 'User')->value('id'),
    ]);
    $response->assertRedirect(route('blindboxes.index', absolute: false));
});
