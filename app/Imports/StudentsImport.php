<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\ParentWallet;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Pehli row ko heading banane ke liye
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // 1. Class ID Find Karna (Excel mein Class Name hoga, e.g., "Nursery")
        $class = SchoolClass::where('class_name', $row['class_name'])->first();
        if (!$class) {
            return null; // Agar class nahi mili toh row skip karo
        }

        DB::beginTransaction();
        try {
            // 2. Parent Logic (Family Grouping)
            $parentUser = User::where('cnic', $row['father_cnic'])->first();

            if (!$parentUser) {
                // Naya Parent Account
                $parentUser = User::create([
                    'name' => $row['father_name'],
                    'email' => $row['father_cnic'] . '@parent.com',
                    'password' => Hash::make('12345678'),
                    'role' => 'parent',
                    'phone' => $row['father_phone'],
                    'cnic' => $row['father_cnic'],
                ]);
                // Naya Wallet
                ParentWallet::create(['parent_user_id' => $parentUser->id]);
            }

            // 3. Student Account Creation
            $studentUser = User::create([
                'name' => $row['full_name'],
                'email' => $row['admission_no'] . '@student.com',
                'password' => Hash::make('12345678'), // Default password for bulk students
                'role' => 'student',
                'phone' => $row['father_phone'],
            ]);

            // 4. Save Student Profile
            $student = new Student([
                'user_id' => $studentUser->id,
                'parent_id' => $parentUser->id, // Linked to parent
                'class_id' => $class->id,
                'admission_no' => $row['admission_no'],
                'roll_no' => $row['roll_no'],
                'full_name' => $row['full_name'],
                'father_name' => $row['father_name'],
                'mother_name' => $row['mother_name'],
                'gender' => $row['gender'],
                'birthday' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['birthday']), // Date fix
                'address' => $row['address'],
                'phone' => $row['father_phone'],
                'admission_date' => now(),
            ]);

            DB::commit();
            return $student;

        } catch (\Exception $e) {
            DB::rollback();
            return null;
        }
    }
}