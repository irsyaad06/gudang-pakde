<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function category(): Category
    {
        return Category::create(['name' => 'Sepeda', 'slug' => 'sepeda']);
    }

    private function fields(Category $category): array
    {
        return [
            'title' => 'Sepeda lipat', 'category_id' => $category->id,
            'price' => 1250000, 'item_condition' => 'good',
            'description' => 'Sepeda terawat', 'location' => 'Bandung',
        ];
    }

    private function listing(User $user, Category $category, array $extra = []): Listing
    {
        return $user->listings()->create([...$this->fields($category), ...$extra]);
    }

    public function test_category_seeder_only_creates_categories(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_guest_can_browse_combined_filters_and_detail_with_minimal_props(): void
    {
        $category = $this->category();
        $other = Category::create(['name' => 'Hobi', 'slug' => 'hobi']);
        $owner = User::factory()->create(['phone' => '6281234567890']);
        $match = $this->listing($owner, $category);
        $match->images()->create(['path' => 'listings/photo.jpg']);
        $this->listing($owner, $category, ['title' => 'Sepeda lain', 'item_condition' => 'fair']);
        $this->listing($owner, $other);
        $this->listing($owner, $category)->delete();

        $this->get('/?search=lipat&category=sepeda&condition=good')
            ->assertOk()->assertInertia(fn (Assert $page) => $page->component('Catalog/Index')
                ->where('filters', ['search' => 'lipat', 'category' => 'sepeda', 'condition' => 'good'])
                ->has('categories', 2)->has('listings.data', 1)
                ->where('listings.data.0.id', $match->id)
                ->where('listings.data.0.category.slug', 'sepeda')
                ->where('listings.data.0.image_url', Storage::disk('public')->url('listings/photo.jpg'))
                ->missing('listings.data.0.user_id')->etc());

        $this->get(route('listings.show', $match))->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Listings/Show')
                ->where('listing.user.name', $owner->name)
                ->where('listing.description', 'Sepeda terawat')
                ->where('listing.whatsapp_url', 'https://wa.me/6281234567890?text='.rawurlencode('Halo, pakde! saya tertarik dengan '.$match->title.' (#'.$match->id.')., saya mau liat detailnya dong...'))
                ->missing('listing.user.phone')->etc());

        $owner->update(['phone' => null]);
        $this->get(route('listings.show', $match))->assertInertia(fn (Assert $page) => $page
            ->where('listing.whatsapp_url', null)->etc());
        $owner->update(['phone' => '6281234567890']);
        $match->update(['status' => 'sold']);
        $this->get(route('listings.show', $match))->assertInertia(fn (Assert $page) => $page
            ->where('listing.whatsapp_url', null)->where('listing.status', 'sold')->etc());
        $match->delete();
        $this->get(route('listings.show', $match))->assertNotFound();
    }

    public function test_catalog_paginates_and_preserves_filters(): void
    {
        $category = $this->category();
        $owner = User::factory()->create();
        for ($i = 0; $i < 13; $i++) {
            $this->listing($owner, $category);
        }
        $this->get('/?category=sepeda&page=2')->assertInertia(fn (Assert $page) => $page
            ->has('listings.data', 1)->where('listings.current_page', 2)
            ->where('listings.prev_page_url', fn ($url) => str_contains($url, 'category=sepeda'))->etc());
    }

    public function test_guest_is_redirected_from_all_management_routes(): void
    {
        $listing = $this->listing(User::factory()->create(), $this->category());
        foreach ([['get', route('listings.create')], ['get', route('listings.mine')],
            ['get', route('listings.edit', $listing)], ['post', route('listings.store')],
            ['patch', route('listings.update', $listing)], ['patch', route('listings.status', $listing)],
            ['delete', route('listings.destroy', $listing)]] as [$method, $url]) {
            $this->$method($url)->assertRedirect(route('login'));
        }
    }

    public function test_seller_requires_phone_and_valid_fields_to_create(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('listings.store'), [
            ...$this->fields($category), 'image' => UploadedFile::fake()->image('photo.jpg'),
        ])->assertSessionHasErrors('phone');
        $this->assertDatabaseCount('listings', 0);
        $user->update(['phone' => '6281234567890']);
        $this->post(route('listings.store'), [
            ...$this->fields($category), 'price' => -1, 'category_id' => 999,
            'item_condition' => 'broken', 'image' => UploadedFile::fake()->create('bad.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors(['price', 'category_id', 'item_condition', 'image']);
        $this->assertDatabaseCount('listing_images', 0);
    }

    public function test_owner_creates_replaces_image_changes_status_and_soft_deletes(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $user = User::factory()->create(['phone' => '6281234567890']);
        $this->actingAs($user)->post(route('listings.store'), [
            ...$this->fields($category), 'image' => UploadedFile::fake()->image('first.jpg'),
        ])->assertSessionHasNoErrors();
        $listing = Listing::firstOrFail();
        $this->assertSame('available', $listing->status);
        $oldPath = $listing->images()->firstOrFail()->path;
        Storage::disk('public')->assertExists($oldPath);
        $this->get(route('listings.edit', $listing))->assertInertia(fn (Assert $page) => $page
            ->component('Listings/Edit')->where('listing.category_id', $category->id)
            ->where('listing.image_url', Storage::disk('public')->url($oldPath))->etc());
        $this->put(route('listings.update', $listing), [
            ...$this->fields($category), 'price' => -1,
        ])->assertSessionHasErrors('price');
        $this->assertSame(1250000, $listing->fresh()->price);
        $this->put(route('listings.update', $listing), [
            ...$this->fields($category), 'price' => 2000000,
        ])->assertSessionHasNoErrors();
        Storage::disk('public')->assertExists($oldPath);
        $this->post(route('listings.store'), [
            ...$this->fields($category), 'image' => UploadedFile::fake()->create('bad.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors('image');
        $this->put(route('listings.update', $listing), [
            ...$this->fields($category), 'image' => UploadedFile::fake()->image('second.png'),
        ])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($listing->images()->firstOrFail()->path);
        $this->patch(route('listings.status', $listing), ['status' => 'lost'])->assertSessionHasErrors('status');
        $this->patch(route('listings.status', $listing), ['status' => 'sold'])->assertSessionHasNoErrors();
        $this->assertSame('sold', $listing->fresh()->status);
        $this->delete(route('listings.destroy', $listing))->assertRedirect(route('listings.mine'));
        $this->assertSoftDeleted('listings', ['id' => $listing->id]);
        Storage::disk('public')->assertExists($listing->images()->firstOrFail()->path);
    }

    public function test_owner_can_manage_multiple_images_and_gallery(): void
    {
        Storage::fake('public');
        $category = $this->category();
        $user = User::factory()->create(['phone' => '6281234567890']);

        // 1. Create with multiple images
        $this->actingAs($user)->post(route('listings.store'), [
            ...$this->fields($category),
            'images' => [
                UploadedFile::fake()->image('img1.jpg'),
                UploadedFile::fake()->image('img2.png'),
                UploadedFile::fake()->image('img3.webp'),
            ],
        ])->assertSessionHasNoErrors();

        $listing = Listing::firstOrFail();
        $this->assertCount(3, $listing->images);
        $firstImageId = $listing->images[0]->id;
        $secondImageId = $listing->images[1]->id;

        // Verify detail page has all images
        $this->get(route('listings.show', $listing))->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Listings/Show')
                ->has('listing.images', 3)
                ->where('listing.images.0.id', $firstImageId)
                ->where('listing.images_count', 3)
                ->etc());

        // 2. Update by deleting first image and adding new one
        $this->put(route('listings.update', $listing), [
            ...$this->fields($category),
            'delete_image_ids' => [$firstImageId],
            'images' => [
                UploadedFile::fake()->image('img4.jpg'),
            ],
        ])->assertSessionHasNoErrors();

        $listing->refresh();
        $this->assertCount(3, $listing->images);
        $this->assertFalse($listing->images->contains('id', $firstImageId));
        $this->assertTrue($listing->images->contains('id', $secondImageId));

        // 3. Validation: Cannot exceed 5 images
        $this->put(route('listings.update', $listing), [
            ...$this->fields($category),
            'images' => [
                UploadedFile::fake()->image('extra1.jpg'),
                UploadedFile::fake()->image('extra2.jpg'),
                UploadedFile::fake()->image('extra3.jpg'),
            ],
        ])->assertSessionHasErrors('images');
    }

    public function test_nonowner_cannot_edit_update_delete_or_change_status(): void
    {
        Storage::fake('public');
        $listing = $this->listing(User::factory()->create(), $this->category());
        $other = User::factory()->create(['phone' => '6281234567890']);
        $this->actingAs($other)->get(route('listings.edit', $listing))->assertForbidden();
        $this->put(route('listings.update', $listing), [
            ...$this->fields($listing->category), 'image' => UploadedFile::fake()->image('intruder.jpg'),
        ])->assertForbidden();
        $this->patch(route('listings.status', $listing), ['status' => 'sold'])->assertForbidden();
        $this->delete(route('listings.destroy', $listing))->assertForbidden();
        $this->assertSame('available', $listing->fresh()->status);
        $this->assertDatabaseCount('listing_images', 0);
        $this->assertSame([], Storage::disk('public')->allFiles('listings'));
    }

    public function test_profile_phone_and_owned_listing_and_account_deletion_rule(): void
    {
        $category = $this->category();
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->listing($other, $category);
        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name, 'email' => $user->email, 'phone' => '+6281234567890',
        ])->assertSessionHasNoErrors();
        $this->assertSame('6281234567890', $user->fresh()->phone);
        $this->patch(route('profile.update'), [
            'name' => $user->name, 'email' => $user->email, 'phone' => '081234567890',
        ])->assertSessionHasErrors('phone');
        $listing = $this->listing($user, $category);
        $this->get(route('profile.edit'))->assertInertia(fn (Assert $page) => $page
            ->component('Profile/Edit')->has('ownedListings.data', 1)
            ->where('ownedListings.data.0.id', $listing->id)->etc());
        $this->get(route('listings.mine'))->assertInertia(fn (Assert $page) => $page
            ->has('listings.data', 1)->where('listings.data.0.id', $listing->id)->etc());
        $listing->delete();
        $this->from('/profile')->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect('/profile')->assertSessionHasErrors('account');
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh());
    }

    public function test_locations_api_and_catalog_location_filter(): void
    {
        // 1. Test /api/locations endpoint
        $res = $this->getJson(route('locations.index', ['search' => 'Bandung']))->assertOk();
        $res->assertJsonFragment(['city' => 'Kota Bandung', 'province' => 'Jawa Barat']);

        $resProv = $this->getJson(route('locations.index', ['type' => 'provinces']))->assertOk();
        $this->assertContains('Jawa Barat', $resProv->json());

        // 2. Test catalog location filter
        $category = $this->category();
        $owner = User::factory()->create();

        $itemBandung = $owner->listings()->create([
            'category_id' => $category->id,
            'title' => 'Sepeda Bandung',
            'price' => 1000000,
            'item_condition' => 'good',
            'description' => 'Lokasi Bandung',
            'location' => 'Kota Bandung, Jawa Barat',
            'status' => 'available',
        ]);
        $itemSurabaya = $owner->listings()->create([
            'category_id' => $category->id,
            'title' => 'Sepeda Surabaya',
            'price' => 1200000,
            'item_condition' => 'good',
            'description' => 'Lokasi Surabaya',
            'location' => 'Kota Surabaya, Jawa Timur',
            'status' => 'available',
        ]);

        $this->get('/?location=Bandung')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Catalog/Index')
            ->where('filters.location', 'Bandung')
            ->has('listings.data', 1)
            ->where('listings.data.0.id', $itemBandung->id)
            ->etc());

        $this->get('/?location=Jawa+Timur')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Catalog/Index')
            ->where('filters.location', 'Jawa Timur')
            ->has('listings.data', 1)
            ->where('listings.data.0.id', $itemSurabaya->id)
            ->etc());
    }
}
