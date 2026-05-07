<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\FeedbackFormTemplate;
use App\Models\Location;
use App\Models\Schedule;
use App\Models\Target;
use App\Models\Territory;
use App\Models\User;
use App\Models\Visit;
use App\Models\Feedback;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Territories ─────────────────────────────────────────
        $dhaka = Territory::create(['name' => 'Dhaka North', 'description' => 'North Dhaka region']);
        $ctg   = Territory::create(['name' => 'Chittagong',  'description' => 'Chittagong port city region']);

        // ── Users ────────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Super Admin', 'email' => 'admin@salestrack.com',
            'password' => Hash::make('password'), 'role' => 'superadmin',
            'phone' => '01700000000', 'employee_id' => 'EMP001', 'is_active' => true,
        ]);

        User::create([
            'name' => 'Karim Manager', 'email' => 'manager@salestrack.com',
            'password' => Hash::make('password'), 'role' => 'manager',
            'phone' => '01711111111', 'employee_id' => 'EMP002',
            'territory_id' => $dhaka->id, 'is_active' => true,
        ]);

        $rep1 = User::create([
            'name' => 'Rafi Ahmed', 'email' => 'rafi@salestrack.com',
            'password' => Hash::make('password'), 'role' => 'salesperson',
            'phone' => '01722222222', 'employee_id' => 'EMP003',
            'territory_id' => $dhaka->id, 'is_active' => true,
            'last_seen_at' => now()->subMinutes(2),
        ]);

        $rep2 = User::create([
            'name' => 'Noor Islam', 'email' => 'noor@salestrack.com',
            'password' => Hash::make('password'), 'role' => 'salesperson',
            'phone' => '01733333333', 'employee_id' => 'EMP004',
            'territory_id' => $ctg->id, 'is_active' => true,
            'last_seen_at' => now()->subMinutes(45),
        ]);

        // ── Clients ──────────────────────────────────────────────
        $c1 = Client::create(['name' => 'Apex Industries Ltd',  'address' => 'Gulshan-1, Dhaka',    'lat' => 23.7925, 'lng' => 90.4078, 'contact_name' => 'Mr. Rahman',    'category' => 'Retail',    'territory_id' => $dhaka->id, 'assigned_to' => $rep1->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01811111111']);
        $c2 = Client::create(['name' => 'Noor Pharma Group',    'address' => 'Motijheel, Dhaka',    'lat' => 23.7280, 'lng' => 90.4170, 'contact_name' => 'Ms. Sultana',   'category' => 'Pharma',    'territory_id' => $dhaka->id, 'assigned_to' => $rep1->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01822222222']);
        $c3 = Client::create(['name' => 'Delta Retail Chain',   'address' => 'Dhanmondi, Dhaka',    'lat' => 23.7461, 'lng' => 90.3742, 'contact_name' => 'Mr. Hossain',   'category' => 'Retail',    'territory_id' => $dhaka->id, 'assigned_to' => $rep1->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01833333333']);
        $c4 = Client::create(['name' => 'Prime Foods Co.',      'address' => 'Mirpur-10, Dhaka',    'lat' => 23.8063, 'lng' => 90.3680, 'contact_name' => 'Mr. Ali',        'category' => 'Food',      'territory_id' => $dhaka->id, 'assigned_to' => $rep1->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01844444444']);
        $c5 = Client::create(['name' => 'Bengal Traders',       'address' => 'Agrabad, Chittagong', 'lat' => 22.3302, 'lng' => 91.8325, 'contact_name' => 'Mr. Chowdhury', 'category' => 'Wholesale', 'territory_id' => $ctg->id,   'assigned_to' => $rep2->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01855555555']);
        $c6 = Client::create(['name' => 'SeaPort Distributors', 'address' => 'Port Area, Ctg',      'lat' => 22.3458, 'lng' => 91.8158, 'contact_name' => 'Ms. Begum',      'category' => 'Wholesale', 'territory_id' => $ctg->id,   'assigned_to' => $rep2->id, 'geofence_radius' => 100, 'is_active' => true, 'contact_phone' => '01866666666']);

        // ── Feedback Form Template ───────────────────────────────
        $template = FeedbackFormTemplate::create([
            'name' => 'Standard Visit Form', 'is_default' => true, 'is_active' => true, 'created_by' => $admin->id,
            'fields' => [
                ['key' => 'outcome',             'label' => 'Visit Outcome',       'type' => 'dropdown', 'options' => ['Order Placed','Follow-Up','Not Interested','Demo Requested'], 'required' => true],
                ['key' => 'satisfaction_rating', 'label' => 'Client Satisfaction', 'type' => 'rating',   'required' => true],
                ['key' => 'competitor_activity', 'label' => 'Competitor Activity', 'type' => 'text',     'required' => false],
                ['key' => 'order_value',         'label' => 'Order Value (BDT)',   'type' => 'number',   'required' => false],
                ['key' => 'notes',               'label' => 'Additional Notes',    'type' => 'textarea', 'required' => false],
            ],
        ]);

        $now = now();

        // ── Visit 1: Apex Industries — completed 09:00–10:00 ────
        $visit1 = Visit::create([
            'salesperson_id' => $rep1->id, 'client_id' => $c1->id,
            'checkin_at'  => $now->copy()->setTime(9, 5),
            'checkout_at' => $now->copy()->setTime(10, 0),
            'checkin_lat' => 23.7925, 'checkin_lng' => 90.4078,
            'checkin_method' => 'auto', 'status' => 'completed',
            'outcome' => 'order_placed', 'rating' => 5,
            'order_value' => 45000,
            'notes' => 'Very interested in Q3 bulk order. Follow up with pricing sheet.',
            'feedback_submitted' => true,
        ]);

        // ── Visit 2: Noor Pharma — completed 11:30–12:15 ────────
        $visit2 = Visit::create([
            'salesperson_id' => $rep1->id, 'client_id' => $c2->id,
            'checkin_at'  => $now->copy()->setTime(11, 30),
            'checkout_at' => $now->copy()->setTime(12, 15),
            'checkin_lat' => 23.7280, 'checkin_lng' => 90.4170,
            'checkin_method' => 'auto', 'status' => 'completed',
            'outcome' => 'follow_up', 'rating' => 4,
            'notes' => 'Requested product demo next week. Decision maker was present.',
            'feedback_submitted' => true,
        ]);

        // ── Visit 3: Delta Retail — in progress 14:00→now ───────
        $visit3 = Visit::create([
            'salesperson_id' => $rep1->id, 'client_id' => $c3->id,
            'checkin_at'  => $now->copy()->setTime(14, 0),
            'checkin_lat' => 23.7461, 'checkin_lng' => 90.3742,
            'checkin_method' => 'auto', 'status' => 'in_progress',
            'feedback_submitted' => false,
        ]);

        // ── Visit 4: Prime Foods — missed ────────────────────────
        Visit::create([
            'salesperson_id' => $rep1->id, 'client_id' => $c4->id,
            'checkin_at' => $now->copy()->setTime(16, 0),
            'checkin_lat' => 23.8063, 'checkin_lng' => 90.3680,
            'status' => 'missed', 'feedback_submitted' => false,
        ]);

        // ── Feedback ─────────────────────────────────────────────
        Feedback::create([
            'visit_id' => $visit1->id, 'salesperson_id' => $rep1->id, 'template_id' => $template->id,
            'data' => ['outcome' => 'Order Placed', 'notes' => 'Q3 bulk order confirmed'],
            'satisfaction_rating' => 5, 'order_value' => 45000,
        ]);
        Feedback::create([
            'visit_id' => $visit2->id, 'salesperson_id' => $rep1->id, 'template_id' => $template->id,
            'data' => ['outcome' => 'Follow-Up', 'notes' => 'Demo scheduled for next week'],
            'competitor_activity' => 'Competitor X offering 5% lower price', 'satisfaction_rating' => 4,
        ]);

        // ── Schedules ────────────────────────────────────────────
        Schedule::create(['salesperson_id' => $rep1->id, 'client_id' => $c1->id, 'scheduled_at' => $now->copy()->setTime(9,  0), 'status' => 'completed', 'sort_order' => 1, 'visit_id' => $visit1->id]);
        Schedule::create(['salesperson_id' => $rep1->id, 'client_id' => $c2->id, 'scheduled_at' => $now->copy()->setTime(11, 30),'status' => 'completed', 'sort_order' => 2, 'visit_id' => $visit2->id]);
        Schedule::create(['salesperson_id' => $rep1->id, 'client_id' => $c3->id, 'scheduled_at' => $now->copy()->setTime(14, 0), 'status' => 'pending',   'sort_order' => 3]);
        Schedule::create(['salesperson_id' => $rep1->id, 'client_id' => $c4->id, 'scheduled_at' => $now->copy()->setTime(16, 0), 'status' => 'pending',   'sort_order' => 4]);

        // ── Targets ──────────────────────────────────────────────
        Target::create(['user_id' => $rep1->id, 'type' => 'daily',   'period_date' => today(), 'visits_target' => 6,  'sales_target' => 100000,  'visits_achieved' => 3, 'sales_achieved' => 45000]);
        Target::create(['user_id' => $rep1->id, 'type' => 'monthly', 'period_date' => today()->startOfMonth(), 'visits_target' => 120, 'sales_target' => 2000000, 'visits_achieved' => 68, 'sales_achieved' => 980000]);
        Target::create(['user_id' => $rep2->id, 'type' => 'daily',   'period_date' => today(), 'visits_target' => 5,  'sales_target' => 80000,   'visits_achieved' => 2, 'sales_achieved' => 30000]);

        // ── GPS Route for Rafi (proper Dhaka street route) ───────
        // Route: Office (Banani) → Gulshan (Client 1) → Malibagh → Motijheel (Client 2)
        //        → Shahbagh → Dhanmondi (Client 3) → Jigatola → Mirpur-10 (Client 4 missed)
        // Each segment has multiple GPS pings ~5 min apart to draw a realistic polyline

        $route = [
            // 08:15 – Leaving office, Banani
            ['t' => $now->copy()->setTime(8, 15), 'lat' => 23.7937, 'lng' => 90.4041],
            ['t' => $now->copy()->setTime(8, 20), 'lat' => 23.7922, 'lng' => 90.4053],
            ['t' => $now->copy()->setTime(8, 25), 'lat' => 23.7918, 'lng' => 90.4065],
            ['t' => $now->copy()->setTime(8, 30), 'lat' => 23.7921, 'lng' => 90.4075],
            // 08:35 – Arrived near Gulshan-1
            ['t' => $now->copy()->setTime(8, 35), 'lat' => 23.7924, 'lng' => 90.4077],

            // 09:05 – At Apex Industries (Visit 1 check-in)
            ['t' => $now->copy()->setTime(9, 5),  'lat' => 23.7925, 'lng' => 90.4078],
            ['t' => $now->copy()->setTime(9, 20), 'lat' => 23.7925, 'lng' => 90.4078],
            ['t' => $now->copy()->setTime(9, 40), 'lat' => 23.7925, 'lng' => 90.4078],
            // 10:00 – Check-out, heading south toward Motijheel
            ['t' => $now->copy()->setTime(10, 0),  'lat' => 23.7925, 'lng' => 90.4078],
            ['t' => $now->copy()->setTime(10, 8),  'lat' => 23.7870, 'lng' => 90.4095],
            ['t' => $now->copy()->setTime(10, 14), 'lat' => 23.7790, 'lng' => 90.4115],
            ['t' => $now->copy()->setTime(10, 20), 'lat' => 23.7710, 'lng' => 90.4130],
            ['t' => $now->copy()->setTime(10, 27), 'lat' => 23.7640, 'lng' => 90.4148],
            ['t' => $now->copy()->setTime(10, 33), 'lat' => 23.7560, 'lng' => 90.4155],
            ['t' => $now->copy()->setTime(10, 40), 'lat' => 23.7480, 'lng' => 90.4162],
            ['t' => $now->copy()->setTime(10, 47), 'lat' => 23.7390, 'lng' => 90.4170],
            ['t' => $now->copy()->setTime(10, 53), 'lat' => 23.7320, 'lng' => 90.4170],
            // 11:00 – Near Motijheel, slowing down
            ['t' => $now->copy()->setTime(11, 0),  'lat' => 23.7290, 'lng' => 90.4172],
            ['t' => $now->copy()->setTime(11, 10), 'lat' => 23.7280, 'lng' => 90.4171],
            ['t' => $now->copy()->setTime(11, 25), 'lat' => 23.7280, 'lng' => 90.4170],

            // 11:30 – At Noor Pharma (Visit 2 check-in)
            ['t' => $now->copy()->setTime(11, 30), 'lat' => 23.7280, 'lng' => 90.4170],
            ['t' => $now->copy()->setTime(11, 50), 'lat' => 23.7280, 'lng' => 90.4170],
            ['t' => $now->copy()->setTime(12, 10), 'lat' => 23.7280, 'lng' => 90.4170],
            // 12:15 – Check-out, heading west toward Dhanmondi
            ['t' => $now->copy()->setTime(12, 15), 'lat' => 23.7280, 'lng' => 90.4170],
            ['t' => $now->copy()->setTime(12, 22), 'lat' => 23.7335, 'lng' => 90.4100],
            ['t' => $now->copy()->setTime(12, 30), 'lat' => 23.7380, 'lng' => 90.4020],
            ['t' => $now->copy()->setTime(12, 38), 'lat' => 23.7400, 'lng' => 90.3940],
            ['t' => $now->copy()->setTime(12, 46), 'lat' => 23.7410, 'lng' => 90.3860],
            ['t' => $now->copy()->setTime(12, 55), 'lat' => 23.7430, 'lng' => 90.3800],
            // 13:00 – Lunch stop near Shahbagh
            ['t' => $now->copy()->setTime(13, 0),  'lat' => 23.7440, 'lng' => 90.3775],
            ['t' => $now->copy()->setTime(13, 15), 'lat' => 23.7440, 'lng' => 90.3775],
            ['t' => $now->copy()->setTime(13, 30), 'lat' => 23.7440, 'lng' => 90.3775],
            ['t' => $now->copy()->setTime(13, 40), 'lat' => 23.7450, 'lng' => 90.3757],
            ['t' => $now->copy()->setTime(13, 50), 'lat' => 23.7455, 'lng' => 90.3748],

            // 14:00 – At Delta Retail, Dhanmondi (Visit 3 — in progress)
            ['t' => $now->copy()->setTime(14, 0),  'lat' => 23.7461, 'lng' => 90.3742],
            ['t' => $now->copy()->setTime(14, 15), 'lat' => 23.7461, 'lng' => 90.3742],
            ['t' => $now->copy()->setTime(14, 30), 'lat' => 23.7461, 'lng' => 90.3742],
            // Still there (most recent ping)
            ['t' => $now->copy()->subMinutes(3),   'lat' => 23.7461, 'lng' => 90.3742],
        ];

        foreach ($route as $p) {
            Location::create([
                'user_id'       => $rep1->id,
                'lat'           => $p['lat'],
                'lng'           => $p['lng'],
                'accuracy'      => round(4 + mt_rand(0, 30) / 10, 1),
                'speed'         => null,
                'battery_level' => 72,
                'recorded_at'   => $p['t'],
            ]);
        }

        // ── Noor (Chittagong) — simple 3-point route ─────────────
        $ctgRoute = [
            ['t' => $now->copy()->setTime(9, 0),  'lat' => 22.3180, 'lng' => 91.8250],
            ['t' => $now->copy()->setTime(9, 20), 'lat' => 22.3240, 'lng' => 91.8290],
            ['t' => $now->copy()->setTime(9, 40), 'lat' => 22.3302, 'lng' => 91.8325],
            ['t' => $now->copy()->setTime(10, 0), 'lat' => 22.3302, 'lng' => 91.8325],
            ['t' => $now->copy()->setTime(10, 30),'lat' => 22.3302, 'lng' => 91.8325],
            ['t' => $now->copy()->setTime(11, 0), 'lat' => 22.3380, 'lng' => 91.8240],
            ['t' => $now->copy()->setTime(11, 20),'lat' => 22.3420, 'lng' => 91.8200],
            ['t' => $now->copy()->setTime(11, 40),'lat' => 22.3458, 'lng' => 91.8158],
            ['t' => $now->copy()->setTime(12, 0), 'lat' => 22.3458, 'lng' => 91.8158],
            ['t' => $now->copy()->subMinutes(45), 'lat' => 22.3458, 'lng' => 91.8158],
        ];
        foreach ($ctgRoute as $p) {
            Location::create([
                'user_id' => $rep2->id, 'lat' => $p['lat'], 'lng' => $p['lng'],
                'accuracy' => 8.0, 'battery_level' => 55, 'recorded_at' => $p['t'],
            ]);
        }
    }
}
