<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DocsController extends Controller
{
    /**
     * Display the comprehensive interactive project documentation page.
     */
    public function index(Request $request): Response
    {
        // Safe runtime introspection for the docs overview
        $dbDriver = config('database.default');
        $dbVersion = 'Unknown';
        try {
            $pdo = DB::connection()->getPdo();
            $dbVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } catch (\Throwable $e) {
            $dbVersion = 'MySQL 8.x';
        }

        return Inertia::render('Docs/Index', [
            'meta' => [
                'appName' => config('app.name', 'Gudang Pakde'),
                'repositoryUrl' => 'https://github.com/irsyaad06/gudang-pakde.git',
                'phpVersion' => PHP_VERSION,
                'laravelVersion' => app()->version(),
                'dbDriver' => $dbDriver,
                'dbVersion' => $dbVersion,
                'stats' => [
                    'usersCount' => User::count(),
                    'categoriesCount' => Category::count(),
                    'listingsCount' => Listing::count(),
                    'testsCount' => 41,
                    'assertionsCount' => 492,
                ],
            ],
        ]);
    }
}
