<?php

namespace Database\Seeders;

use App\Models\BillingCode;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('payments')->truncate();
        DB::table('invoice_items')->truncate();
        DB::table('invoices')->truncate();
        
        // Get biller user ID
        $billerId = User::role('biller')->first()?->id ?? 1;
        
        // Get billing codes for invoice items
        $billingCodes = BillingCode::where('type', 'cpt')->get();
        
        // Check if we have billing codes
        if ($billingCodes->isEmpty()) {
            $this->command->warn('No billing codes found. Skipping invoice generation.');
            return;
        }
        
        // Get encounters that are either completed or billed
        $encounters = Encounter::whereIn('status', ['completed', 'billed'])->get();
        
        // Check if we have encounters
        if ($encounters->isEmpty()) {
            $this->command->warn('No completed or billed encounters found. Skipping invoice generation.');
            return;
        }
        
        // Payment methods
        $paymentMethods = ['Credit Card', 'Insurance', 'Cash', 'Check', 'Bank Transfer'];
        
        // For each encounter, create an invoice
        foreach ($encounters as $encounter) {
            // Only create invoices for some completed encounters
            if ($encounter->status === 'completed' && rand(0, 10) < 7) {
                continue;
            }
            
            // Generate invoice number
            $invoiceNumber = 'ENT-INV-' . date('Ym', strtotime($encounter->encounter_date)) . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
            // Set invoice dates
            $invoiceDate = Carbon::parse($encounter->encounter_date)->addDays(rand(1, 3));
            $dueDate = (clone $invoiceDate)->addDays(30);
            
            // Create the invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $encounter->patient_id,
                'encounter_id' => $encounter->id,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'total_amount' => 0, // Will calculate after adding items
                'amount_paid' => 0, // Will update after adding payments
                'amount_due' => 0, // Will calculate after adding items and payments
                'status' => 'draft',
                'notes' => rand(0, 1) ? 'Standard invoice for ENT services.' : null,
                'created_by' => $billerId,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);
            
            // Add invoice items based on encounter type
            $totalAmount = 0;
            
            // Always add an office visit charge (safely)
            $officeVisitCodes = $billingCodes->filter(function ($code) {
                return strpos($code->code, '992') === 0;  // Codes that start with 992
            });
            
            // If no specific office visit codes, use any billing code
            if ($officeVisitCodes->isEmpty()) {
                $officeVisitCode = $billingCodes->first();
            } else {
                $officeVisitCode = $officeVisitCodes->random();
            }
            
            $officeVisitPrice = $officeVisitCode->default_price;
            $totalAmount += $officeVisitPrice;
            
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'billing_code_id' => $officeVisitCode->id,
                'description' => $officeVisitCode->description,
                'quantity' => 1,
                'unit_price' => $officeVisitPrice,
                'total_price' => $officeVisitPrice,
                'created_at' => $invoiceDate,
                'updated_at' => $invoiceDate,
            ]);
            
            // Add procedure charges based on the encounter assessment
            $procedureAdded = false;
            
            if (stripos($encounter->assessment, 'otitis') !== false) {
                // Ear infection - possibly add ear wax removal
                if (rand(0, 1)) {
                    $procedureCode = $billingCodes->where('code', '69210')->first();
                    if ($procedureCode) {
                        $procedurePrice = $procedureCode->default_price;
                        $totalAmount += $procedurePrice;
                        
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'billing_code_id' => $procedureCode->id,
                            'description' => $procedureCode->description,
                            'quantity' => 1,
                            'unit_price' => $procedurePrice,
                            'total_price' => $procedurePrice,
                            'created_at' => $invoiceDate,
                            'updated_at' => $invoiceDate,
                        ]);
                        $procedureAdded = true;
                    }
                }
            } elseif (stripos($encounter->assessment, 'sinusitis') !== false) {
                // Sinusitis - possibly add nasal endoscopy
                if (rand(0, 1)) {
                    $procedureCode = $billingCodes->where('code', '31231')->first();
                    if ($procedureCode) {
                        $procedurePrice = $procedureCode->default_price;
                        $totalAmount += $procedurePrice;
                        
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'billing_code_id' => $procedureCode->id,
                            'description' => $procedureCode->description,
                            'quantity' => 1,
                            'unit_price' => $procedurePrice,
                            'total_price' => $procedurePrice,
                            'created_at' => $invoiceDate,
                            'updated_at' => $invoiceDate,
                        ]);
                        $procedureAdded = true;
                    }
                }
            } elseif (stripos($encounter->assessment, 'tonsillitis') !== false) {
                // Tonsillitis - possibly add laryngoscopy
                if (rand(0, 1)) {
                    $procedureCode = $billingCodes->where('code', '31575')->first();
                    if ($procedureCode) {
                        $procedurePrice = $procedureCode->default_price;
                        $totalAmount += $procedurePrice;
                        
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'billing_code_id' => $procedureCode->id,
                            'description' => $procedureCode->description,
                            'quantity' => 1,
                            'unit_price' => $procedurePrice,
                            'total_price' => $procedurePrice,
                            'created_at' => $invoiceDate,
                            'updated_at' => $invoiceDate,
                        ]);
                        $procedureAdded = true;
                    }
                }
            }
            
            // If no specific procedure added, possibly add a general or random procedure
            if (!$procedureAdded && rand(0, 1)) {
                // Safely get a procedure code (any code if specific ones don't exist)
                $desiredCodes = ['99000', '31231', '69210'];
                $procedureCodes = $billingCodes->whereIn('code', $desiredCodes);
                
                if ($procedureCodes->isEmpty()) {
                    $procedureCode = $billingCodes->random(); // Use any available code
                } else {
                    $procedureCode = $procedureCodes->random();
                }
                
                $procedurePrice = $procedureCode->default_price;
                $totalAmount += $procedurePrice;
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'billing_code_id' => $procedureCode->id,
                    'description' => $procedureCode->description,
                    'quantity' => 1,
                    'unit_price' => $procedurePrice,
                    'total_price' => $procedurePrice,
                    'created_at' => $invoiceDate,
                    'updated_at' => $invoiceDate,
                ]);
            }
            
            // Update invoice with total amount
            $invoice->total_amount = $totalAmount;
            $invoice->amount_due = $totalAmount;
            
            // Determine invoice status and payments
            $daysSinceCreated = Carbon::now()->diffInDays($invoiceDate);
            $isPastDue = $daysSinceCreated > 30;
            
            if ($encounter->status === 'billed') {
                if (rand(0, 10) <= 7) { // 70% chance of paid invoices for billed encounters
                    // Fully paid
                    $paymentDate = Carbon::parse($invoiceDate)->addDays(rand(1, 15));
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $totalAmount,
                        'payment_date' => $paymentDate,
                        'payment_method' => $paymentMethod,
                        'transaction_id' => strtoupper(substr(md5(rand()), 0, 10)),
                        'notes' => rand(0, 1) ? 'Payment received in full.' : null,
                        'recorded_by' => $billerId,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ]);
                    
                    $invoice->amount_paid = $totalAmount;
                    $invoice->amount_due = 0;
                    $invoice->status = 'paid';
                    $invoice->updated_at = $paymentDate;
                } elseif (rand(0, 10) <= 5) { // 50% chance of partial payment for remaining
                    // Partially paid
                    $paymentAmount = round($totalAmount * (rand(30, 70) / 100), 2);
                    $paymentDate = Carbon::parse($invoiceDate)->addDays(rand(1, 15));
                    $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                    
                    Payment::create([
                        'invoice_id' => $invoice->id,
                        'amount' => $paymentAmount,
                        'payment_date' => $paymentDate,
                        'payment_method' => $paymentMethod,
                        'transaction_id' => strtoupper(substr(md5(rand()), 0, 10)),
                        'notes' => 'Partial payment received.',
                        'recorded_by' => $billerId,
                        'created_at' => $paymentDate,
                        'updated_at' => $paymentDate,
                    ]);
                    
                    $invoice->amount_paid = $paymentAmount;
                    $invoice->amount_due = $totalAmount - $paymentAmount;
                    $invoice->status = $isPastDue ? 'overdue' : 'partially_paid';
                    $invoice->updated_at = $paymentDate;
                } else {
                    // Not paid
                    $invoice->status = $isPastDue ? 'overdue' : 'sent';
                }
            } else {
                // Draft invoice for completed but not billed encounters
                $invoice->status = 'draft';
            }
            
            $invoice->save();
            
            // If encounter was not already billed, update it to billed if invoice is paid/sent
            if ($encounter->status !== 'billed' && in_array($invoice->status, ['paid', 'partially_paid', 'sent'])) {
                $encounter->status = 'billed';
                $encounter->save();
            }
        }
    }
}
