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
            'can' => $request->user()
                ? [
                    'view_users' => $request->user()->can('view users'),
                    'create_users' => $request->user()->can('create users'),
                    'edit_users' => $request->user()->can('edit users'),
                    'delete_users' => $request->user()->can('delete users'),
                    'view_roles' => $request->user()->can('view roles'),
                    'create_roles' => $request->user()->can('create roles'),
                    'edit_roles' => $request->user()->can('edit roles'),
                    'delete_roles' => $request->user()->can('delete roles'),
                ]
                : [],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
