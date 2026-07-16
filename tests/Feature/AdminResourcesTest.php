<?php

namespace Tests\Feature;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Cities\CityResource;
use App\Filament\Resources\Countries\CountryResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\States\StateResource;
use App\Filament\Resources\Tags\TagResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Category;
use App\Models\Country;
use App\Models\Post;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_resource_pages_render(): void
    {
        $user = User::factory()->create();
        $category = Category::query()->create(['name' => 'News', 'slug' => 'news']);
        $post = Post::query()->create([
            'title' => 'Test post',
            'slug' => 'test-post',
            'category_id' => $category->getKey(),
            'body' => 'Content',
        ]);
        $product = Product::query()->create([
            'name' => 'Test product',
            'sku' => 'TEST-001',
            'price' => 10,
            'stock' => 5,
        ]);

        $this->actingAs($user);

        foreach ([
            '/admin/posts',
            '/admin/posts/create',
            "/admin/posts/{$post->getKey()}/edit",
            '/admin/tags',
            '/admin/tags/create',
            '/admin/products',
            '/admin/products/create',
            "/admin/products/{$product->getKey()}",
            '/admin/users',
            '/admin/users/create',
            "/admin/users/{$user->getKey()}/edit",
            '/admin/countries',
            "/admin/categories/{$category->getKey()}/edit",
            '/admin/states',
            '/admin/cities',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_content_and_location_relations_are_persisted(): void
    {
        $category = Category::query()->create(['name' => 'News', 'slug' => 'news']);
        $tag = Tag::query()->create(['name' => 'Laravel', 'slug' => 'laravel']);
        $post = Post::query()->create([
            'title' => 'First post',
            'slug' => 'first-post',
            'category_id' => $category->getKey(),
            'body' => 'Content',
        ]);
        $post->tags()->attach($tag);

        $country = Country::query()->create(['name' => 'Palestine']);
        $state = $country->states()->create(['name' => 'Hebron']);
        $city = $state->cities()->create(['name' => 'Hebron']);
        $user = User::factory()->create([
            'country_id' => $country->getKey(),
            'state_id' => $state->getKey(),
            'city_id' => $city->getKey(),
        ]);

        $this->assertTrue($post->tags->contains($tag));
        $this->assertTrue($category->posts->contains($post));
        $this->assertTrue($user->country->is($country));
        $this->assertTrue($user->state->is($state));
        $this->assertTrue($user->city->is($city));

        $country->delete();
        $user->refresh();

        $this->assertNull($user->country_id);
        $this->assertNull($user->state_id);
        $this->assertNull($user->city_id);
    }

    public function test_global_search_finds_all_managed_resource_types(): void
    {
        $country = Country::query()->create(['name' => 'Searchland']);
        $state = $country->states()->create(['name' => 'Search State']);
        $city = $state->cities()->create(['name' => 'Search City']);
        $user = User::factory()->create([
            'name' => 'Search User',
            'country_id' => $country->getKey(),
            'state_id' => $state->getKey(),
            'city_id' => $city->getKey(),
        ]);
        $category = Category::query()->create(['name' => 'Search Category', 'slug' => 'search-category']);
        Tag::query()->create(['name' => 'Search Tag', 'slug' => 'search-tag']);
        Post::query()->create([
            'title' => 'Search Post',
            'slug' => 'search-post',
            'category_id' => $category->getKey(),
            'body' => 'Content',
        ]);
        Product::query()->create([
            'name' => 'Search Product',
            'sku' => 'SEARCH-001',
            'price' => 15,
            'stock' => 3,
        ]);

        $this->actingAs($user);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        foreach ([
            PostResource::class,
            CategoryResource::class,
            TagResource::class,
            ProductResource::class,
            UserResource::class,
            CountryResource::class,
            StateResource::class,
            CityResource::class,
        ] as $resource) {
            $this->assertNotEmpty($resource::getGlobalSearchResults('Search'));
        }

        $groupLabels = collect(Filament::getNavigationGroups())
            ->map(fn ($group) => $group->getLabel())
            ->all();

        $this->assertSame(['Content', 'Commerce', 'Administration', 'Locations'], $groupLabels);
        $this->assertSame('Posts', CategoryResource::getNavigationParentItem());
        $this->assertSame('Posts', TagResource::getNavigationParentItem());
    }
}
