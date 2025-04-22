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
                    // User/Role permissions
                    'view_users' => $request->user()->can('view users'),
                    'create_users' => $request->user()->can('create users'),
                    'edit_users' => $request->user()->can('edit users'),
                    'delete_users' => $request->user()->can('delete users'),
                    'view_roles' => $request->user()->can('view roles'),
                    'create_roles' => $request->user()->can('create roles'),
                    'edit_roles' => $request->user()->can('edit roles'),
                    'delete_roles' => $request->user()->can('delete roles'),
                    
                    // Patient permissions
                    'view_patients' => $request->user()->can('view patients'),
                    'create_patients' => $request->user()->can('create patients'),
                    'edit_patients' => $request->user()->can('edit patients'),
                    'delete_patients' => $request->user()->can('delete patients'),
                    
                    // Appointment permissions
                    'view_appointments' => $request->user()->can('view appointments'),
                    'create_appointments' => $request->user()->can('create appointments'),
                    'edit_appointments' => $request->user()->can('edit appointments'),
                    'delete_appointments' => $request->user()->can('delete appointments'),
                    
                    // Encounter permissions
                    'view_encounters' => $request->user()->can('view encounters'),
                    'create_encounters' => $request->user()->can('create encounters'),
                    'edit_encounters' => $request->user()->can('edit encounters'),
                    'delete_encounters' => $request->user()->can('delete encounters'),
                    
                    // Invoice/billing permissions
                    'view_invoices' => $request->user()->can('view invoices'),
                    'create_invoices' => $request->user()->can('create invoices'),
                    'edit_invoices' => $request->user()->can('edit invoices'),
                    'delete_invoices' => $request->user()->can('delete invoices'),
                    'manage_billing_codes' => $request->user()->can('manage billing codes'),
                    
                    // Report permissions
                    'view_reports' => $request->user()->can('view reports'),
                    'export_reports' => $request->user()->can('export reports'),
                ]
                : [],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
