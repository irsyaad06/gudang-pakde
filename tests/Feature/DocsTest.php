<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DocsTest extends TestCase
{
    use RefreshDatabase;

    public function test_documentation_page_can_be_rendered(): void
    {
        $response = $this->get(route('docs.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Docs/Index')
            ->has('meta.appName')
            ->has('meta.repositoryUrl')
            ->has('meta.phpVersion')
            ->has('meta.laravelVersion')
            ->has('meta.stats.usersCount')
            ->has('meta.stats.categoriesCount')
            ->has('meta.stats.listingsCount')
        );
    }
}
