<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            // Session flashes → Inertia props, so redirect()-with()-messages
            // actually render (permission denials, CRUD success, etc.).
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            // App-wide permission truth for UI gating. Each `can_*` mirrors a route
            // gate in routes/web.php, so a button can never outlive its permission.
            'can' => [
                'force_delete' => $request->user()?->hasPermission('force_delete') ?? false,
                'crud_assets'  => $request->user()?->hasPermission('crud_assets') ?? false,
            ],
        ];
    }
}
