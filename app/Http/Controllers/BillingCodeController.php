<?php

namespace App\Http\Controllers;

use App\Models\BillingCode;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage billing codes');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BillingCode::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->input('code_type'), function ($query, $codeType) {
                $query->where('code_type', $codeType);
            })
            ->when($request->input('sort_field') && $request->input('sort_direction'), function ($query) use ($request) {
                $query->orderBy($request->input('sort_field'), $request->input('sort_direction'));
            }, function ($query) {
                $query->orderBy('code_type')->orderBy('code');
            });

        return Inertia::render('BillingCodes/Index', [
            'billingCodes' => $query->paginate(15)->withQueryString(),
            'filters' => $request->only(['search', 'code_type', 'sort_field', 'sort_direction']),
            'codeTypes' => [
                'CPT' => 'CPT',
                'ICD-10' => 'ICD-10',
                'HCPCS' => 'HCPCS'
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('BillingCodes/Create', [
            'codeTypes' => [
                'CPT' => 'CPT (Current Procedural Terminology)',
                'ICD-10' => 'ICD-10 (International Classification of Diseases)',
                'HCPCS' => 'HCPCS (Healthcare Common Procedure Coding System)'
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:billing_codes,code',
            'description' => 'required|string|max:255',
            'code_type' => 'required|in:CPT,ICD-10,HCPCS',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        BillingCode::create($validated);
        
        return redirect()->route('billing-codes.index')
            ->with('message', 'Billing code created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BillingCode $billingCode)
    {
        return Inertia::render('BillingCodes/Show', [
            'billingCode' => $billingCode
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BillingCode $billingCode)
    {
        return Inertia::render('BillingCodes/Edit', [
            'billingCode' => $billingCode,
            'codeTypes' => [
                'CPT' => 'CPT (Current Procedural Terminology)',
                'ICD-10' => 'ICD-10 (International Classification of Diseases)',
                'HCPCS' => 'HCPCS (Healthcare Common Procedure Coding System)'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BillingCode $billingCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:billing_codes,code,' . $billingCode->id,
            'description' => 'required|string|max:255',
            'code_type' => 'required|in:CPT,ICD-10,HCPCS',
            'price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);
        
        $billingCode->update($validated);
        
        return redirect()->route('billing-codes.index')
            ->with('message', 'Billing code updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BillingCode $billingCode)
    {
        $billingCode->delete();
        
        return redirect()->route('billing-codes.index')
            ->with('message', 'Billing code deleted successfully.');
    }
}