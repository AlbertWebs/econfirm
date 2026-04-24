<?php

test('developer login page renders', function () {
    $response = $this->get(route('developer.login'));

    $response->assertOk();
    $response->assertSee('API developer', false);
});

test('developer register page renders', function () {
    $response = $this->get(route('developer.register'));

    $response->assertOk();
    $response->assertSee('API developer sign up', false);
    $response->assertSee(route('developer.register.submit'), false);
});

test('guest visiting developer portal is redirected to developer login', function () {
    $response = $this->get(route('api.home'));

    $response->assertRedirect(route('developer.login'));
});

test('invalid developer login redirects back to developer login', function () {
    $response = $this->from(route('developer.login'))->post(route('developer.login.submit'), [
        'email' => 'nobody@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('developer.login'));
    $this->assertGuest();
});

test('api documentation page shows contact phone from site settings', function () {
    $display = (string) site_setting('contact_phone_display', '0748 349995');

    $response = $this->get(route('api-documentation'));

    $response->assertOk();
    $response->assertSee($display, false);
});
