<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\DemoListingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use RuntimeException;
use Tests\TestCase;

class DemoListingSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->withoutVite();
    }

    public function test_explicit_seed_is_repeatable_and_images_work_in_catalog_and_detail(): void
    {
        $this->seed(DemoListingSeeder::class);
        $this->seed(DemoListingSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('listings', 6);
        $this->assertDatabaseCount('listing_images', 6);
        $seller = User::where('email', DemoListingSeeder::SELLER_EMAIL)->firstOrFail();
        $this->assertNull($seller->phone);
        $this->assertSame(2, Listing::where('status', 'sold')->count());
        $this->assertSame(4, Listing::where('status', 'available')->count());

        foreach (Listing::with(['images', 'category'])->get() as $listing) {
            $image = $listing->images->sole();
            $this->assertSame('demo/gudang-pakde/', substr($image->path, 0, 18));
            Storage::disk('public')->assertExists($image->path);
            $this->assertStringContainsString('<svg', Storage::disk('public')->get($image->path));
            $this->assertStringContainsString('/storage/demo/gudang-pakde/', $listing->cardData()['image_url']);
            $this->get(route('listings.show', $listing))->assertOk()
                ->assertInertia(fn (Assert $page) => $page->component('Listings/Show')
                    ->where('listing.whatsapp_url', null)->etc());
        }

        $this->get('/?category=sepeda&condition=good')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')
                ->has('listings.data', 1)->where('listings.data.0.title', 'Sepeda Lipat 20 Inci untuk Komuter')->etc());
    }

    public function test_seed_preserves_non_demo_data_and_existing_demo_edits_and_deletions(): void
    {
        $owner = User::factory()->create(['phone' => null]);
        $category = Category::create(['name' => 'Sepeda Komunitas', 'slug' => 'sepeda']);
        $original = $owner->listings()->create([
            'category_id' => $category->id, 'title' => 'Sepeda milik anggota',
            'price' => 123456, 'item_condition' => 'fair',
            'description' => 'Barang asli pengguna', 'location' => 'Bogor', 'status' => 'available',
        ]);
        Storage::disk('public')->put('listings/user-photo.jpg', 'existing user content');
        $original->images()->create(['path' => 'listings/user-photo.jpg']);

        $this->seed(DemoListingSeeder::class);
        $demo = Listing::where('title', 'Kursi Kerja Sandaran Jaring')->firstOrFail();
        $demo->update(['price' => 700000]);
        $removed = Listing::where('title', 'Helm Sepeda Urban Ukuran M')->firstOrFail();
        $removed->delete();
        $this->seed(DemoListingSeeder::class);

        $this->assertSame(123456, $original->fresh()->price);
        $this->assertSame('existing user content', Storage::disk('public')->get('listings/user-photo.jpg'));
        $this->assertSame('Sepeda Komunitas', $category->fresh()->name);
        $this->assertSame(700000, $demo->fresh()->price);
        $this->assertNotNull(Listing::withTrashed()->findOrFail($removed->id)->deleted_at);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('listings', 7);
        $this->assertDatabaseCount('listing_images', 7);
    }

    public function test_production_refuses_to_seed_before_writing_anything(): void
    {
        $previous = app()->environment();
        app()->instance('env', 'production');

        try {
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('local or testing');
            try {
                (new DemoListingSeeder)->run();
            } finally {
                $this->assertDatabaseCount('users', 0);
                $this->assertDatabaseCount('listings', 0);
                $this->assertSame([], Storage::disk('public')->allFiles());
            }
        } finally {
            app()->instance('env', $previous);
        }
    }
}
