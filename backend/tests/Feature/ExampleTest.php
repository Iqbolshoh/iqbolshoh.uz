<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What Laravel actually owns on this domain.
 *
 * The site itself is a React build served straight by nginx, so "/" is not a
 * Laravel route at all and never was — the stock test that asserted a 200 there
 * had been failing since the day the panel was split out. These two assert the
 * real contract instead: the API answers, and the panel exists behind auth.
 */
class ExampleTest extends TestCase
{
    // Both of these make a request, so the schema has to exist — and without
    // this the rows they create would leak into whichever class runs next.
    use RefreshDatabase;

    public function test_the_public_api_answers(): void
    {
        $this->get('/api/content')->assertOk();
    }

    public function test_the_admin_panel_is_behind_authentication(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }
}
