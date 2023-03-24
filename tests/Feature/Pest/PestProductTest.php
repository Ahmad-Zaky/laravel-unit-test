<?php

namespace Tests\Feature\Pest;

uses(\Tests\TestCase::class);

test('unauthenticated user cannot access products', function () {
    $this->get(route("products.index"))
        ->assertRedirect(route("login"));
});

