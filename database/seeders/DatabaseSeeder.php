<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\FeeType;
use App\Models\ExpenseCategory;
use App\Models\Campus;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Main Campus
        $campus = Campus::create([
            'campus_name' => 'Main Campus',
            'address' => 'Shahpur Sadar',
            'contact_number' => '0300-1234567',
            'principal_name' => 'Principal Sahab'
        ]);

        // 2. Create Super Admin User (Login ke liye)
        User::create([
            'campus_id' => $campus->id,
            'name' => 'Super Admin',
            'email' => 'admin@school.com', // Login ID
            'password' => Hash::make('password'), // Password
            'role' => 'admin',
            'status' => 1
        ]);

        // 3. Default School Settings
        Setting::create([
            'school_name' => 'Paradise Public Girls Elementary School',
            'current_session' => '2026-2027',
            'currency_symbol' => 'PKR'
        ]);

        // 4. Create Basic Classes
        $classes = ['Nursery', 'Prep', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight'];
        foreach ($classes as $index => $name) {
            SchoolClass::create([
                'class_name' => $name,
                'numeric_name' => $index
            ]);
        }

        // 5. Default Fee Types
        FeeType::create(['fee_title' => 'Monthly Tuition Fee', 'default_amount' => 2000]);
        FeeType::create(['fee_title' => 'Admission Fee', 'default_amount' => 5000]);
        FeeType::create(['fee_title' => 'Exam Fee', 'default_amount' => 500]);

        // 6. Default Expense Categories
        ExpenseCategory::create(['category_name' => 'Utility Bills']);
        ExpenseCategory::create(['category_name' => 'Staff Salaries']); // System uses ID 1 or 2 usually, keep safe
        ExpenseCategory::create(['category_name' => 'Maintenance']);
        ExpenseCategory::create(['category_name' => 'Stationery']);
    }
}