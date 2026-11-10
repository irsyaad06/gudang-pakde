<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingRequest;
use App\Models\Category;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ListingController extends Controller
{
    public function mine(Request $request): Response
    {
        return Inertia::render('Listings/MyListings', [
            'listings' => $request->user()->listings()->with(['category', 'images'])
                ->latest()->paginate(12)->through(fn (Listing $listing) => $listing->cardData()),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Listings/Create', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(ListingRequest $request): RedirectResponse
    {
        if (! preg_match('/^62[1-9][0-9]{7,12}$/', $request->user()->phone ?? '')) {
            throw ValidationException::withMessages(['phone' => 'Tambahkan nomor WhatsApp valid (62...) di profil sebelum memasang barang.']);
        }

        $data = $request->safe()->except('image');
        $path = $request->file('image')->store('listings', 'public');
        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan foto barang.');
        }
        try {
            $listing = DB::transaction(function () use ($request, $data, $path) {
                $listing = $request->user()->listings()->create($data);
                $listing->images()->create(['path' => $path]);

                return $listing;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);
            throw $exception;
        }

        return redirect()->route('listings.show', $listing)->with('success', 'Barang berhasil dipasang.');
    }

    public function edit(Listing $listing): Response
    {
        Gate::authorize('update', $listing);
        $listing->load(['category', 'images']);

        return Inertia::render('Listings/Edit', [
            'listing' => [...$listing->cardData(), 'description' => $listing->description, 'category_id' => $listing->category_id],
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function update(ListingRequest $request, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $data = $request->safe()->except('image');
        $path = $request->hasFile('image') ? $request->file('image')->store('listings', 'public') : null;
        if ($path === false) {
            throw new \RuntimeException('Gagal menyimpan foto barang.');
        }
        $oldPath = null;

        try {
            DB::transaction(function () use ($listing, $data, $path, &$oldPath) {
                $listing->update($data);
                if ($path !== null) {
                    $image = $listing->images()->first();
                    $oldPath = $image?->path;
                    if ($image) {
                        $image->update(['path' => $path]);
                    } else {
                        $listing->images()->create(['path' => $path]);
                    }
                }
            });
        } catch (Throwable $exception) {
            if ($path !== null) {
                Storage::disk('public')->delete($path);
            }
            throw $exception;
        }

        if ($oldPath !== null) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('listings.show', $listing)->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        Gate::authorize('delete', $listing);
        $listing->delete();

        return redirect()->route('listings.mine')->with('success', 'Barang berhasil dihapus.');
    }

    public function status(Request $request, Listing $listing): RedirectResponse
    {
        Gate::authorize('update', $listing);
        $data = $request->validate(['status' => ['required', Rule::in(['available', 'sold'])]]);
        $listing->update($data);

        return back()->with('success', 'Status barang berhasil diperbarui.');
    }
}
