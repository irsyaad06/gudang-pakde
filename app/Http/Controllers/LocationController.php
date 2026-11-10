<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private static ?array $data = null;

    private function loadData(): array
    {
        if (self::$data === null) {
            $path = resource_path('data/indonesia-locations.json');
            if (file_exists($path)) {
                self::$data = json_decode(file_get_contents($path), true) ?: ['provinces' => [], 'cities' => []];
            } else {
                self::$data = ['provinces' => [], 'cities' => []];
            }
        }

        return self::$data;
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->loadData();
        $search = strtolower(trim((string) $request->query('search', $request->query('q', ''))));
        $province = trim((string) $request->query('province', ''));
        $type = $request->query('type');

        if ($type === 'provinces') {
            $provinces = $data['provinces'] ?? [];
            if ($search !== '') {
                $provinces = array_values(array_filter($provinces, fn ($p) => str_contains(strtolower($p), $search)));
            }
            return response()->json($provinces);
        }

        $cities = $data['cities'] ?? [];

        if ($province !== '') {
            $cities = array_values(array_filter($cities, fn ($c) => strcasecmp($c['province'], $province) === 0));
        }

        if ($search !== '') {
            $cities = array_values(array_filter($cities, function ($c) use ($search) {
                return str_contains(strtolower($c['city']), $search)
                    || str_contains(strtolower($c['province']), $search)
                    || str_contains(strtolower($c['label']), $search);
            }));
        }

        // Limit results to 40 for optimal payload and performance
        $results = array_slice($cities, 0, 40);

        return response()->json($results);
    }
}
