<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\Classes;
use App\Models\Lessons;
use App\Models\Enrollments;
use App\Models\Payment;
use App\Models\Receipt;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run()
    {
        // Create a user + student
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'student1@example.com',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $student = Student::create([
            'StudentID' => 'S0001',
            'user_id' => $user->id,
            'studentName' => 'John Doe',
            'phoneNum' => '0123456789',
            'address' => '123 Main St',
            'gender' => 'Male',
        ]);

        // Create a class
        $class = Classes::create([
            'ClassID' => 'C0001',
            'SubjectID' => 'SUB001',
        ]);

        // Add lessons for the class
        Lessons::create([
            'lesson_id' => 1,
            'ClassID' => $class->ClassID,
            'start' => '2025-09-01 10:00:00',
            'ends' => '2025-09-01 12:00:00',
            'room' => 'Room A',
        ]);

        // Enroll student into the class
        Enrollments::create([
            'enrollment_id' => 1,
            'ClassID' => $class->ClassID,
            'StudentID' => $student->StudentID,
        ]);

        // Add a payment
        $payment = Payment::create([
            'PaymentID' => 'P0001',
            'StudentID' => $student->StudentID,
            'paymentTotal' => 200,
            'paymentDate' => now(),
            'status' => 'Paid',
        ]);

        // Add a receipt
        Receipt::create([
            'ReceiptID' => 'R0001',
            'PaymentID' => $payment->PaymentID,
            'subTotal' => 200,
            'receiptDate' => now(),
        ]);
    }
}
