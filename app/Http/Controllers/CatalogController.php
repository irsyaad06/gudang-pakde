<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'string', 'max:120'],
            'condition' => ['nullable', Rule::in(['new', 'like_new', 'good', 'fair'])],
            'location' => ['nullable', 'string', 'max:200'],
        ]);

        $search = trim($filters['search'] ?? '');
        $category = $filters['category'] ?? null;
        $condition = $filters['condition'] ?? null;
        $location = trim($filters['location'] ?? '');

        $listings = Listing::query()
            ->with(['category', 'images'])
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            }))
            ->when($category, fn ($query) => $query->whereHas('category', fn ($query) => $query->where('slug', $category)))
            ->when($condition, fn ($query) => $query->where('item_condition', $condition))
            ->when($location !== '', fn ($query) => $query->where('location', 'like', '%'.$location.'%'))
            ->latest()->paginate(12)->withQueryString()
            ->through(fn (Listing $listing) => $listing->cardData());

        $activeFilters = [
            'search' => $search,
            'category' => $category,
            'condition' => $condition,
        ];
        if ($location !== '') {
            $activeFilters['location'] = $location;
        }

        return Inertia::render('Catalog/Index', [
            'listings' => $listings,
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
            'filters' => $activeFilters,
        ]);
    }

    public function show(Listing $listing): Response
    {
        $listing->load(['category', 'images', 'user:id,name,phone']);
        $phone = $listing->user->phone;
        $whatsappUrl = $listing->status === 'available' && is_string($phone)
            && preg_match('/^62[1-9][0-9]{7,12}$/', $phone)
                ? 'https://wa.me/'.$phone.'?text='.rawurlencode('Halo, pakde! saya tertarik dengan '.$listing->title.' (#'.$listing->id.').'. ', saya mau liat detailnya dong...')
                : null;

        return Inertia::render('Listings/Show', [
            'listing' => [
                ...$listing->cardData(),
                'description' => $listing->description,
                'user' => ['name' => $listing->user->name],
                'whatsapp_url' => $whatsappUrl,
            ],
        ]);
    }
}
