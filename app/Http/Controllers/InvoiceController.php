<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Encounter;
use App\Models\BillingCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::query()
            ->with(['patient', 'items'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhere('total_amount', 'like', "%{$search}%")
                        ->orWhereHas('patient', function ($query) use ($search) {
                            $query->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->input('date_from'), function ($query, $date) {
                $query->whereDate('invoice_date', '>=', $date);
            })
            ->when($request->input('date_to'), function ($query, $date) {
                $query->whereDate('invoice_date', '<=', $date);
            })
            ->when($request->input('min_amount'), function ($query, $amount) {
                $query->where('total', '>=', $amount);
            })
            ->when($request->input('max_amount'), function ($query, $amount) {
                $query->where('total', '<=', $amount);
            })
            ->when($request->input('sort_field') && $request->input('sort_direction'), function ($query) use ($request) {
                $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
            }, function ($query) {
                $query->latest('invoice_date');
            });

        return Inertia::render('Invoices/Index', [
            'invoices' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'min_amount', 'max_amount', 'sort_field', 'sort_direction']),
            'statuses' => [
                'draft' => 'Draft',
                'sent' => 'Sent',
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                'cancelled' => 'Cancelled'
            ]
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Invoice::class);
        
        // Get all patients for the dropdown
        $patients = Patient::select('id', 'first_name', 'last_name', 'patient_id')->get();
        
        // If an encounter_id was provided in the query params, load that encounter
        $encounter = null;
        $patientId = null;
        
        if ($request->has('encounter_id')) {
            $encounter = Encounter::with('patient')->find($request->encounter_id);
            if ($encounter) {
                $patientId = $encounter->patient_id;
            }
        } elseif ($request->has('patient_id')) {
            $patientId = $request->patient_id;
        }
        
        // Load encounters ready for billing
        $encounters = Encounter::where('status', 'completed')
            ->whereDoesntHave('invoice')
            ->when($patientId, function($query) use ($patientId) {
                $query->where('patient_id', $patientId);
            })
            ->with('patient')
            ->get();
            
        // Get all available billing codes
        $billingCodes = BillingCode::all();
        
        return Inertia::render('Invoices/Create', [
            'patients' => $patients,
            'encounters' => $encounters,
            'selectedEncounter' => $encounter,
            'billingCodes' => $billingCodes,
            'dueInDays' => 30, // Default due date is 30 days from now
            'statuses' => [
                'draft' => 'Draft',
                'sent' => 'Sent',
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                'cancelled' => 'Cancelled'
            ]
        ]);
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Invoice::class);
        
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|string|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.billing_code_id' => 'required|exists:billing_codes,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        
        // Generate the invoice number
        $invoiceNumber = Invoice::generateInvoiceNumber();
        
        // Create the invoice
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'patient_id' => $validated['patient_id'],
            'encounter_id' => $validated['encounter_id'] ?? null,
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
            'total_amount' => 0, // Will be updated by updateTotals
            'amount_paid' => 0,
            'amount_due' => 0, 
        ]);
        
        // Add items to the invoice
        foreach ($validated['items'] as $item) {
            $billingCode = BillingCode::find($item['billing_code_id']);
            
            $invoice->items()->create([
                'billing_code_id' => $item['billing_code_id'],
                'code' => $billingCode->code,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }
        
        // Update invoice totals
        $invoice->updateTotals();
        
        // If this invoice is related to an encounter, mark the encounter as billed
        if ($invoice->encounter) {
            $invoice->encounter->update(['status' => 'billed']);
        }
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        
        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice->load(['patient', 'encounter', 'items', 'payments']),
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        // Don't allow editing if the invoice is paid
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Paid invoices cannot be edited.');
        }
        
        $patients = Patient::select('id', 'first_name', 'last_name', 'patient_id')->get();
        $billingCodes = BillingCode::all();
        
        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice->load(['patient', 'encounter', 'items']),
            'patients' => $patients,
            'billingCodes' => $billingCodes,
            'statuses' => [
                'draft' => 'Draft',
                'sent' => 'Sent',
                'paid' => 'Paid',
                'overdue' => 'Overdue',
                'cancelled' => 'Cancelled'
            ]
        ]);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);
        
        // Don't allow updating if the invoice is paid
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Paid invoices cannot be updated.');
        }
        
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|string|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:invoice_items,id',
            'items.*.billing_code_id' => 'required|exists:billing_codes,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);
        
        // Update invoice details
        $invoice->update([
            'invoice_date' => $validated['invoice_date'],
            'due_date' => $validated['due_date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Handle invoice items
        $currentItemIds = $invoice->items->pluck('id')->toArray();
        $updatedItemIds = collect($validated['items'])->pluck('id')->filter()->toArray();
        
        // Delete items that were removed
        $itemsToDelete = array_diff($currentItemIds, $updatedItemIds);
        if (!empty($itemsToDelete)) {
            $invoice->items()->whereIn('id', $itemsToDelete)->delete();
        }
        
        // Update existing items and add new ones
        foreach ($validated['items'] as $item) {
            $billingCode = BillingCode::find($item['billing_code_id']);
            $itemData = [
                'billing_code_id' => $item['billing_code_id'],
                'code' => $billingCode->code,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ];
            
            if (isset($item['id']) && $item['id']) {
                $invoice->items()->where('id', $item['id'])->update($itemData);
            } else {
                $invoice->items()->create($itemData);
            }
        }
        
        // Update invoice totals
        $invoice->updateTotals();
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        
        // Don't allow deleting if the invoice is paid
        if ($invoice->status === 'paid') {
            return redirect()->route('invoices.index')
                ->with('error', 'Paid invoices cannot be deleted.');
        }
        
        // If this invoice is related to an encounter, mark it as completed (not billed anymore)
        if ($invoice->encounter && $invoice->encounter->status === 'billed') {
            $invoice->encounter->update(['status' => 'completed']);
        }
        
        // Delete the invoice and related items
        $invoice->items()->delete();
        $invoice->delete();
        
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}