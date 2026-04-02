<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_generate_unique_slugs_for_colliding_names(): void
    {
        $first = Category::create([
            'name' => 'C',
            'color' => '#111111',
        ]);

        $second = Category::create([
            'name' => 'C#',
            'color' => '#222222',
        ]);

        $this->assertSame('c', $first->slug);
        $this->assertSame('c-1', $second->slug);
    }

    public function test_tags_generate_unique_slugs_for_colliding_names(): void
    {
        $first = Tag::create([
            'name' => 'C',
        ]);

        $second = Tag::create([
            'name' => 'C++',
        ]);

        $this->assertSame('c', $first->slug);
        $this->assertSame('c-1', $second->slug);
    }
}
