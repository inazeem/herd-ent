<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::query()
            ->with(['client', 'items'])
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('quote_number', 'like', "%{$search}%")
                        ->orWhere('total', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->input('date_from'), function ($query, $date) {
                $query->whereDate('quote_date', '>=', $date);
            })
            ->when($request->input('date_to'), function ($query, $date) {
                $query->whereDate('quote_date', '<=', $date);
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
                $query->latest('quote_date');
            });

        return Inertia::render('Quotes/Index', [
            'quotes' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to', 'min_amount', 'max_amount', 'sort_field', 'sort_direction']),
            'statuses' => [
                'draft' => 'Draft',
                'sent' => 'Sent',
                'accepted' => 'Accepted',
                'declined' => 'Declined',
                'expired' => 'Expired'
            ]
        ]);
    }
} 