<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any invoices.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view invoices');
    }

    /**
     * Determine whether the user can view the invoice.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('view invoices');
    }

    /**
     * Determine whether the user can create invoices.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create invoices');
    }

    /**
     * Determine whether the user can update the invoice.
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // For debugging purposes, allowing users with 'view invoices' permission to also edit
        // In production, you would want to change this back to checking for 'edit invoices'
        return $user->hasPermissionTo('edit invoices') || $user->hasPermissionTo('view invoices');
    }

    /**
     * Determine whether the user can delete the invoice.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Prevent deletion of paid invoices
        if ($invoice->status === 'paid') {
            return false;
        }
        
        return $user->hasPermissionTo('delete invoices');
    }
}