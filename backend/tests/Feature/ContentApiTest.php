<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The one request the public site makes.
 *
 * It is the whole site's content in a single payload — 125 KB of JSON, 25 KB on
 * the wire — and every visitor's every page load asks for it. Without a
 * validator it is re-downloaded in full each time; with one, an unchanged
 * payload costs a 304 and no body at all.
 *
 * Deliberately no `max-age`: the payload is already cached for a minute on the
 * server, and a browser cache on top of that would double how long an edit made
 * in the panel takes to appear.
 */
class ContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_payload_carries_a_validator(): void
    {
        $response = $this->get('/api/content');

        $response->assertOk();

        $this->assertNotNull($response->headers->get('ETag'), '/api/content has nothing to revalidate against');
    }

    public function test_an_unchanged_payload_costs_nothing(): void
    {
        $etag = $this->get('/api/content')->headers->get('ETag');

        $response = $this->withHeaders(['If-None-Match' => $etag])->get('/api/content');

        $response->assertStatus(304);
        $this->assertSame('', $response->getContent(), 'a 304 must not carry the body it just saved');
    }

    /**
     * A browser cache here would outlive the server's own, so an edit made in
     * the panel would sit invisible for twice as long as intended.
     */
    public function test_the_payload_is_never_cached_past_a_change(): void
    {
        $control = (string) $this->get('/api/content')->headers->get('Cache-Control');

        $this->assertStringNotContainsString('max-age=', $control);
    }
}
