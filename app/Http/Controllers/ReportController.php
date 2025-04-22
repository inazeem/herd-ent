<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\BillingCode;
use App\Models\Encounter;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view reports');
    }

    /**
     * Display a listing of the reports available
     */
    public function index()
    {
        return Inertia::render('Reports/Index', [
            'reports' => [
                [
                    'title' => 'Dashboard',
                    'description' => 'Overview of key metrics and activities',
                    'route' => 'reports.dashboard'
                ],
                [
                    'title' => 'Financial Report',
                    'description' => 'Revenue, payments, and outstanding invoices',
                    'route' => 'reports.financial'
                ],
                [
                    'title' => 'Clinical Report',
                    'description' => 'Patient encounters, procedures, and diagnoses',
                    'route' => 'reports.clinical'
                ],
                [
                    'title' => 'Patient Demographics',
                    'description' => 'Patient age, gender, and insurance data',
                    'route' => 'reports.patients'
                ],
                [
                    'title' => 'Appointment Analytics',
                    'description' => 'Appointment trends, no-shows, and cancellations',
                    'route' => 'reports.appointments'
                ],
                [
                    'title' => 'User Performance',
                    'description' => 'Provider productivity and performance metrics',
                    'route' => 'reports.users'
                ]
            ]
        ]);
    }

    /**
     * Show the dashboard with summary reports
     */
    public function dashboard()
    {
        // Get counts for dashboard with trends (% change from last month)
        $currentMonth = Carbon::now()->month;
        $lastMonth = Carbon::now()->subMonth()->month;

        $patientCount = Patient::count();
        $patientCountLastMonth = Patient::whereMonth('created_at', $lastMonth)->count();
        $patientCountThisMonth = Patient::whereMonth('created_at', $currentMonth)->count();
        $patientGrowth = $patientCountLastMonth > 0 
            ? round((($patientCountThisMonth - $patientCountLastMonth) / $patientCountLastMonth) * 100, 1) 
            : 0;

        $appointmentCount = Appointment::count();
        $appointmentCountLastMonth = Appointment::whereMonth('date', $lastMonth)->count();
        $appointmentCountThisMonth = Appointment::whereMonth('date', $currentMonth)->count();
        $appointmentGrowth = $appointmentCountLastMonth > 0 
            ? round((($appointmentCountThisMonth - $appointmentCountLastMonth) / $appointmentCountLastMonth) * 100, 1) 
            : 0;

        $encounterCount = Encounter::count();
        $encounterCountLastMonth = Encounter::whereMonth('encounter_date', $lastMonth)->count();
        $encounterCountThisMonth = Encounter::whereMonth('encounter_date', $currentMonth)->count();
        $encounterGrowth = $encounterCountLastMonth > 0 
            ? round((($encounterCountThisMonth - $encounterCountLastMonth) / $encounterCountLastMonth) * 100, 1) 
            : 0;

        $invoiceCount = Invoice::count();
        $invoiceCountLastMonth = Invoice::whereMonth('invoice_date', $lastMonth)->count();
        $invoiceCountThisMonth = Invoice::whereMonth('invoice_date', $currentMonth)->count();
        $invoiceGrowth = $invoiceCountLastMonth > 0 
            ? round((($invoiceCountThisMonth - $invoiceCountLastMonth) / $invoiceCountLastMonth) * 100, 1) 
            : 0;

        // Revenue metrics
        $totalRevenue = Invoice::sum('total_amount');
        $collectedRevenue = Invoice::sum('paid_amount');
        $outstandingRevenue = $totalRevenue - $collectedRevenue;
        $revenueLastMonth = Invoice::whereMonth('invoice_date', $lastMonth)->sum('total_amount');
        $revenueThisMonth = Invoice::whereMonth('invoice_date', $currentMonth)->sum('total_amount');
        $revenueGrowth = $revenueLastMonth > 0 
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1) 
            : 0;

        // Get today's appointments with more details
        $todayAppointments = Appointment::whereDate('date', Carbon::today())
            ->with('patient', 'clinician')
            ->orderBy('start_time')
            ->get();

        // Get recent invoices with more details
        $recentInvoices = Invoice::with('patient', 'items.billingCode')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Calendar data - get all appointments for the current month
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $calendarAppointments = Appointment::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('patient:id,first_name,last_name', 'clinician:id,name')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'title' => $appointment->patient->first_name . ' ' . $appointment->patient->last_name,
                    'start' => $appointment->date->format('Y-m-d') . 'T' . $appointment->start_time,
                    'end' => $appointment->date->format('Y-m-d') . 'T' . $appointment->end_time,
                    'status' => $appointment->status,
                    'provider' => $appointment->clinician->name,
                    'url' => '/appointments/' . $appointment->id,
                ];
            });

        // Get appointments by day for the next 30 days
        $thirtyDaysFromNow = Carbon::today()->addDays(30);
        $upcomingAppointments = Appointment::whereBetween('date', [
                Carbon::today(),
                $thirtyDaysFromNow
            ])
            ->select(DB::raw('DATE(date) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) {
                return $item->count;
            });

        // Quick stats - recent activity
        $newPatientsToday = Patient::whereDate('created_at', Carbon::today())->count();
        $completedAppointmentsToday = Appointment::whereDate('date', Carbon::today())
            ->where('status', 'completed')
            ->count();
        $noShowsToday = Appointment::whereDate('date', Carbon::today())
            ->where('status', 'no-show')
            ->count();
        $invoicesToday = Invoice::whereDate('created_at', Carbon::today())->count();
        $revenueTodayAmount = Invoice::whereDate('created_at', Carbon::today())->sum('total_amount');
        
        // Provider performance snapshot
        $providerPerformance = User::role('clinician')
            ->withCount(['appointments' => function($query) {
                $query->whereMonth('date', Carbon::now()->month);
            }])
            ->withCount(['encounters' => function($query) {
                $query->whereMonth('encounter_date', Carbon::now()->month);
            }])
            ->get()
            ->take(5);
            
        // Calculate revenue manually since there's no total_charge column
        foreach ($providerPerformance as $provider) {
            // Get all encounters for this provider this month
            $encounterIds = Encounter::where('clinician_id', $provider->id)
                ->whereMonth('encounter_date', Carbon::now()->month)
                ->pluck('id');
                
            // Calculate total revenue from invoices associated with these encounters
            $revenue = Invoice::whereIn('encounter_id', $encounterIds)->sum('total_amount');
            $provider->revenue = $revenue;
        }
        
        // Sort by encounter count and limit to top 5
        $providerPerformance = $providerPerformance->sortByDesc('encounters_count')->values()->take(5);

        return Inertia::render('Dashboard', [
            'counts' => [
                'patients' => [
                    'total' => $patientCount,
                    'growth' => $patientGrowth,
                    'thisMonth' => $patientCountThisMonth
                ],
                'appointments' => [
                    'total' => $appointmentCount,
                    'growth' => $appointmentGrowth,
                    'thisMonth' => $appointmentCountThisMonth
                ],
                'encounters' => [
                    'total' => $encounterCount,
                    'growth' => $encounterGrowth,
                    'thisMonth' => $encounterCountThisMonth
                ],
                'invoices' => [
                    'total' => $invoiceCount,
                    'growth' => $invoiceGrowth,
                    'thisMonth' => $invoiceCountThisMonth
                ],
            ],
            'revenue' => [
                'total' => $totalRevenue,
                'collected' => $collectedRevenue,
                'outstanding' => $outstandingRevenue,
                'growth' => $revenueGrowth,
                'thisMonth' => $revenueThisMonth
            ],
            'todayStats' => [
                'newPatients' => $newPatientsToday,
                'completedAppointments' => $completedAppointmentsToday,
                'noShows' => $noShowsToday,
                'invoices' => $invoicesToday,
                'revenue' => $revenueTodayAmount,
            ],
            'todayAppointments' => $todayAppointments,
            'recentInvoices' => $recentInvoices,
            'calendarAppointments' => $calendarAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'providerPerformance' => $providerPerformance,
            'today' => Carbon::today()->toDateString(),
            'startOfMonth' => $startOfMonth->toDateString(),
            'endOfMonth' => $endOfMonth->toDateString(),
        ]);
    }

    /**
     * Show the financial report page
     */
    public function financialReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // Revenue by day
        $revenue = Invoice::whereBetween('invoice_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(invoice_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue by billing code
        $revenueByCode = Invoice::join('invoice_items', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('billing_codes', 'invoice_items.billing_code_id', '=', 'billing_codes.id')
            ->whereBetween('invoices.invoice_date', [$startDate, $endDate])
            ->select(
                'billing_codes.code',
                'billing_codes.description',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(invoice_items.amount) as total')
            )
            ->groupBy('billing_codes.id', 'billing_codes.code', 'billing_codes.description')
            ->orderByDesc('total')
            ->get();

        // Payment collection summary
        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->select(
                DB::raw('payment_method'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('payment_method')
            ->get();

        // Outstanding invoices
        $outstandingInvoices = Invoice::whereColumn('total_amount', '>', 'paid_amount')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->with('patient')
            ->orderByDesc('invoice_date')
            ->limit(10)
            ->get();

        return Inertia::render('Reports/FinancialReport', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'revenue' => $revenue,
            'revenueByCode' => $revenueByCode,
            'payments' => $payments,
            'outstandingInvoices' => $outstandingInvoices,
            'summary' => [
                'totalRevenue' => $revenue->sum('total'),
                'totalCollected' => $payments->sum('total'),
                'outstandingAmount' => Invoice::whereBetween('invoice_date', [$startDate, $endDate])
                    ->sum(DB::raw('total_amount - paid_amount')),
            ]
        ]);
    }

    /**
     * Show the clinical activity report
     */
    public function clinicalReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        
        // Encounters by clinician
        $encountersByUser = Encounter::whereBetween('encounter_date', [$startDate, $endDate])
            ->join('users', 'encounters.clinician_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('count')
            ->get();

        // Most common diagnoses/procedures
        $commonProcedures = Encounter::whereBetween('encounter_date', [$startDate, $endDate])
            ->join('encounter_billing_code', 'encounters.id', '=', 'encounter_billing_code.encounter_id')
            ->join('billing_codes', 'encounter_billing_code.billing_code_id', '=', 'billing_codes.id')
            ->where('billing_codes.code_type', 'CPT')
            ->select(
                'billing_codes.code',
                'billing_codes.description',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('billing_codes.id', 'billing_codes.code', 'billing_codes.description')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Most common diagnoses
        $commonDiagnoses = Encounter::whereBetween('encounter_date', [$startDate, $endDate])
            ->join('encounter_billing_code', 'encounters.id', '=', 'encounter_billing_code.encounter_id')
            ->join('billing_codes', 'encounter_billing_code.billing_code_id', '=', 'billing_codes.id')
            ->where('billing_codes.code_type', 'ICD')
            ->select(
                'billing_codes.code',
                'billing_codes.description',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('billing_codes.id', 'billing_codes.code', 'billing_codes.description')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Encounter counts by day
        $encountersByDay = Encounter::whereBetween('encounter_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(encounter_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return Inertia::render('Reports/ClinicalReport', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'encountersByUser' => $encountersByUser,
            'commonProcedures' => $commonProcedures,
            'commonDiagnoses' => $commonDiagnoses,
            'encountersByDay' => $encountersByDay,
            'summary' => [
                'totalEncounters' => $encountersByDay->sum('count'),
                'averagePerDay' => $encountersByDay->avg('count'),
            ]
        ]);
    }

    /**
     * Show the patient demographics report
     */
    public function patientReport()
    {
        // Age distribution (0-18, 19-35, 36-50, 51-65, 66+)
        $ageDistribution = DB::select(DB::raw("
            SELECT 
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 0 AND 18 THEN '0-18'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 19 AND 35 THEN '19-35'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 36 AND 50 THEN '36-50'
                    WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 51 AND 65 THEN '51-65'
                    ELSE '66+'
                END as age_group,
                COUNT(*) as count
            FROM patients
            GROUP BY age_group
            ORDER BY age_group
        "));

        // Gender distribution
        $genderDistribution = Patient::select('gender', DB::raw('count(*) as count'))
            ->groupBy('gender')
            ->get();

        // Insurance distribution
        $insuranceDistribution = Patient::select('insurance_provider', DB::raw('count(*) as count'))
            ->groupBy('insurance_provider')
            ->orderByDesc('count')
            ->get();

        // New patients by month (last 12 months)
        $newPatientsByMonth = Patient::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Zip code distribution (top 10)
        $zipCodeDistribution = Patient::select('zip_code', DB::raw('count(*) as count'))
            ->groupBy('zip_code')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return Inertia::render('Reports/PatientReport', [
            'ageDistribution' => $ageDistribution,
            'genderDistribution' => $genderDistribution,
            'insuranceDistribution' => $insuranceDistribution,
            'newPatientsByMonth' => $newPatientsByMonth,
            'zipCodeDistribution' => $zipCodeDistribution,
            'summary' => [
                'totalPatients' => Patient::count(),
                'newPatientsThisMonth' => Patient::where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
                'patientRetentionRate' => $this->calculatePatientRetentionRate(),
            ]
        ]);
    }

    /**
     * Calculate the patient retention rate
     * (Patients with more than one appointment / Total patients with appointments) * 100
     */
    private function calculatePatientRetentionRate()
    {
        $totalPatientsWithAppointments = DB::table('appointments')
            ->distinct('patient_id')
            ->count('patient_id');

        if ($totalPatientsWithAppointments === 0) {
            return 0;
        }

        $patientsWithMultipleAppointments = DB::table('appointments')
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        return round(($patientsWithMultipleAppointments / $totalPatientsWithAppointments) * 100, 1);
    }

    /**
     * Show the appointment analytics report
     */
    public function appointmentReport()
    {
        // Appointments by status (confirmed, completed, cancelled, no-show)
        $appointmentsByStatus = Appointment::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        // Appointments by day of week
        $appointmentsByDayOfWeek = DB::select(DB::raw("
            SELECT 
                DAYNAME(date) as day_name,
                COUNT(*) as count
            FROM appointments
            GROUP BY day_name
            ORDER BY FIELD(day_name, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
        "));

        // Appointments by hour of day
        $appointmentsByHour = DB::select(DB::raw("
            SELECT 
                HOUR(start_time) as hour,
                COUNT(*) as count
            FROM appointments
            GROUP BY hour
            ORDER BY hour
        "));

        // Monthly trend (last 12 months)
        $appointmentsByMonth = Appointment::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m') as month"),
                DB::raw('count(*) as count')
            )
            ->where('date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Calculate no-show and cancellation rates
        $totalAppointments = Appointment::count();
        $noShowCount = Appointment::where('status', 'no-show')->count();
        $cancelledCount = Appointment::where('status', 'cancelled')->count();
        
        $noShowRate = $totalAppointments > 0 ? round(($noShowCount / $totalAppointments) * 100, 1) : 0;
        $cancellationRate = $totalAppointments > 0 ? round(($cancelledCount / $totalAppointments) * 100, 1) : 0;

        // Average appointment duration
        $avgDuration = Appointment::whereNotNull('end_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration')
            ->first()
            ->avg_duration ?? 0;

        // Average lead time (days between scheduling and appointment)
        $avgLeadTime = Appointment::whereNotNull('created_at')
            ->selectRaw('AVG(DATEDIFF(date, created_at)) as avg_lead_time')
            ->first()
            ->avg_lead_time ?? 0;

        return Inertia::render('Reports/AppointmentReport', [
            'appointmentsByStatus' => $appointmentsByStatus,
            'appointmentsByDayOfWeek' => $appointmentsByDayOfWeek,
            'appointmentsByHour' => $appointmentsByHour,
            'appointmentsByMonth' => $appointmentsByMonth,
            'summary' => [
                'totalAppointments' => $totalAppointments,
                'appointmentsThisMonth' => Appointment::whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year)
                    ->count(),
                'noShowRate' => $noShowRate,
                'cancellationRate' => $cancellationRate,
                'averageDuration' => round($avgDuration),
                'averageLeadTime' => round($avgLeadTime),
            ]
        ]);
    }

    /**
     * Show the user performance report
     */
    public function userReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $userId = $request->input('user_id');

        // Base query for providers with the clinician role
        $providersQuery = User::role('clinician')
            ->withCount(['appointments' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->withCount(['appointments as completed_appointments_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'completed');
            }])
            ->withCount(['appointments as no_show_appointments_count' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                      ->where('status', 'no-show');
            }])
            ->withCount(['encounters' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('encounter_date', [$startDate, $endDate]);
            }]);

        // If specific user is requested, filter for that user
        if ($userId) {
            $providersQuery->where('id', $userId);
        }

        $providers = $providersQuery->orderByDesc('encounters_count')->get();

        // Calculate revenue metrics for each provider
        foreach ($providers as $provider) {
            // Get all encounters for this provider within the date range
            $encounterIds = Encounter::where('clinician_id', $provider->id)
                ->whereBetween('encounter_date', [$startDate, $endDate])
                ->pluck('id');
            
            // Calculate revenue from invoices linked to these encounters
            $revenue = Invoice::whereIn('encounter_id', $encounterIds)->sum('total_amount');
            $provider->revenue = $revenue;
            
            // Calculate no-show rate
            $provider->no_show_rate = $provider->appointments_count > 0
                ? round(($provider->no_show_appointments_count / $provider->appointments_count) * 100, 1)
                : 0;

            // Calculate completion rate
            $provider->completion_rate = $provider->appointments_count > 0
                ? round(($provider->completed_appointments_count / $provider->appointments_count) * 100, 1)
                : 0;

            // Calculate average revenue per encounter
            $provider->avg_revenue_per_encounter = $provider->encounters_count > 0
                ? round($revenue / $provider->encounters_count, 2)
                : 0;

            // Get patient count for this provider
            $provider->patient_count = DB::table('appointments')
                ->where('clinician_id', $provider->id)
                ->distinct('patient_id')
                ->count('patient_id');
        }

        // Daily activity for selected provider
        $dailyActivity = null;
        if ($userId) {
            $dailyActivity = Appointment::where('clinician_id', $userId)
                ->whereBetween('date', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(date) as date'),
                    DB::raw('COUNT(*) as appointments'),
                    DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed'),
                    DB::raw('SUM(CASE WHEN status = "no-show" THEN 1 ELSE 0 END) as no_shows')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Get most common billing codes for this provider
            $commonBillingCodes = DB::table('encounters')
                ->join('encounter_billing_code', 'encounters.id', '=', 'encounter_billing_code.encounter_id')
                ->join('billing_codes', 'encounter_billing_code.billing_code_id', '=', 'billing_codes.id')
                ->where('encounters.clinician_id', $userId)
                ->whereBetween('encounters.encounter_date', [$startDate, $endDate])
                ->select(
                    'billing_codes.code',
                    'billing_codes.description',
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('billing_codes.id', 'billing_codes.code', 'billing_codes.description')
                ->orderByDesc('count')
                ->limit(10)
                ->get();
        }

        // Average metrics for comparison
        $averageMetrics = [
            'appointments' => $providers->avg('appointments_count'),
            'encounters' => $providers->avg('encounters_count'),
            'revenue' => $providers->avg('revenue'),
            'noShowRate' => $providers->avg('no_show_rate'),
            'completionRate' => $providers->avg('completion_rate'),
            'avgRevenuePerEncounter' => $providers->avg('avg_revenue_per_encounter'),
        ];

        // Get the top performer in each category
        $topPerformers = [
            'appointments' => $providers->sortByDesc('appointments_count')->first(),
            'encounters' => $providers->sortByDesc('encounters_count')->first(),
            'revenue' => $providers->sortByDesc('revenue')->first(),
            'completionRate' => $providers->sortByDesc('completion_rate')->first(),
        ];

        return Inertia::render('Reports/UserReport', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedUserId' => $userId,
            'providers' => $providers,
            'dailyActivity' => $dailyActivity,
            'commonBillingCodes' => $commonBillingCodes ?? null,
            'averageMetrics' => $averageMetrics,
            'topPerformers' => $topPerformers,
        ]);
    }
}