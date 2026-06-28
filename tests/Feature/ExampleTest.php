<?php

it('redirects to the dashboard', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('dashboard', absolute: false));
});
