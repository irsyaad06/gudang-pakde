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

        $data = $request->safe()->except(['image', 'images', 'delete_image_ids']);

        $uploadedFiles = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $uploadedFiles[] = $file;
                }
            }
        }
        if ($request->hasFile('image')) {
            $uploadedFiles[] = $request->file('image');
        }

        if (empty($uploadedFiles)) {
            throw ValidationException::withMessages(['images' => 'Wajib mengunggah minimal satu foto barang.']);
        }

        $storedPaths = [];
        foreach ($uploadedFiles as $file) {
            $path = $file->store('listings', 'public');
            if ($path === false) {
                foreach ($storedPaths as $p) {
                    Storage::disk('public')->delete($p);
                }
                throw new \RuntimeException('Gagal menyimpan foto barang.');
            }
            $storedPaths[] = $path;
        }

        try {
            $listing = DB::transaction(function () use ($request, $data, $storedPaths) {
                $listing = $request->user()->listings()->create($data);
                foreach ($storedPaths as $index => $path) {
                    $listing->images()->create([
                        'path' => $path,
                        'sort_order' => $index,
                    ]);
                }

                return $listing;
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $p) {
                Storage::disk('public')->delete($p);
            }
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
        $data = $request->safe()->except(['image', 'images', 'delete_image_ids']);

        $singleReplacement = $request->hasFile('image') ? $request->file('image') : null;

        $newFiles = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    $newFiles[] = $file;
                }
            }
        }

        $deleteImageIds = array_filter(array_map('intval', (array) $request->input('delete_image_ids', [])));
        $imagesToDelete = !empty($deleteImageIds)
            ? $listing->images()->whereIn('id', $deleteImageIds)->get()
            : collect();

        if (! $singleReplacement) {
            $currentCount = $listing->images()->count();
            $remainingCount = ($currentCount - $imagesToDelete->count()) + count($newFiles);
            if ($remainingCount < 1) {
                throw ValidationException::withMessages(['images' => 'Barang harus memiliki minimal satu foto.']);
            }
            if ($remainingCount > 5) {
                throw ValidationException::withMessages(['images' => 'Maksimal 5 foto barang yang diperbolehkan.']);
            }
        }

        $singlePath = null;
        if ($singleReplacement) {
            $singlePath = $singleReplacement->store('listings', 'public');
            if ($singlePath === false) {
                throw new \RuntimeException('Gagal menyimpan foto barang.');
            }
        }

        $storedNewPaths = [];
        foreach ($newFiles as $file) {
            $path = $file->store('listings', 'public');
            if ($path === false) {
                if ($singlePath) {
                    Storage::disk('public')->delete($singlePath);
                }
                foreach ($storedNewPaths as $p) {
                    Storage::disk('public')->delete($p);
                }
                throw new \RuntimeException('Gagal menyimpan foto barang.');
            }
            $storedNewPaths[] = $path;
        }

        $pathsToDelete = [];

        try {
            DB::transaction(function () use ($listing, $data, $singlePath, $storedNewPaths, $imagesToDelete, &$pathsToDelete) {
                $listing->update($data);

                if ($singlePath !== null) {
                    $allExisting = $listing->images()->get();
                    foreach ($allExisting as $old) {
                        $pathsToDelete[] = $old->path;
                        $old->delete();
                    }
                    $listing->images()->create([
                        'path' => $singlePath,
                        'sort_order' => 0,
                    ]);
                } else {
                    foreach ($imagesToDelete as $toDelete) {
                        $pathsToDelete[] = $toDelete->path;
                        $toDelete->delete();
                    }

                    $maxOrder = $listing->images()->max('sort_order') ?? -1;
                    foreach ($storedNewPaths as $idx => $p) {
                        $listing->images()->create([
                            'path' => $p,
                            'sort_order' => $maxOrder + 1 + $idx,
                        ]);
                    }
                }
            });
        } catch (Throwable $exception) {
            if ($singlePath !== null) {
                Storage::disk('public')->delete($singlePath);
            }
            foreach ($storedNewPaths as $p) {
                Storage::disk('public')->delete($p);
            }
            throw $exception;
        }

        foreach ($pathsToDelete as $oldP) {
            Storage::disk('public')->delete($oldP);
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
