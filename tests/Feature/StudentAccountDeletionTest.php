<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StudentAccount;
use App\Models\PatientUpload;
use App\Models\PatientInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class StudentAccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that when a student account is deleted, 
     * their uploaded files are also deleted from storage.
     */
    public function test_student_deletion_removes_uploaded_files(): void
    {
        // Fake the local storage (private)
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info for the student
        $patientInfo = PatientInfo::create([
            'student_account_id' => $student->id,
            'last_name' => 'Doe',
            'first_name' => 'John',
            'middle_name' => 'M',
            'birthdate' => '2000-01-01',
            'age' => 25,
            'sex' => 'Male',
            'address' => '123 Test St',
            'contact_no' => '09123456789',
            'course' => 'BSIT',
            'year_level' => '4th Year',
            'status' => 'approved',
        ]);

        // Create a fake uploaded file
        $file = UploadedFile::fake()->image('patient_photo.jpg');
        $filePath = $file->store('private/patient_uploads', 'local');

        // Create a patient upload record
        $upload = PatientUpload::create([
            'student_account_id' => $student->id,
            'patient_id' => $patientInfo->id,
            'file_path' => $filePath,
            'original_name' => 'patient_photo.jpg',
            'file_size' => $file->getSize(),
        ]);

        // Verify the file exists in storage
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Verify the database record exists
        $this->assertDatabaseHas('patient_uploads', [
            'id' => $upload->id,
            'student_account_id' => $student->id,
        ]);

        // Delete the student account
        $student->delete();

        // Verify the database record is deleted (cascade delete)
        $this->assertDatabaseMissing('patient_uploads', [
            'id' => $upload->id,
        ]);

        // Verify the physical file is deleted from storage
        $this->assertFalse(Storage::disk('local')->exists($filePath));
    }

    /**
     * Test that deleting a single upload also removes the physical file.
     */
    public function test_individual_upload_deletion_removes_file(): void
    {
        // Fake the local storage (private)
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info for the student
        $patientInfo = PatientInfo::create([
            'student_account_id' => $student->id,
            'last_name' => 'Smith',
            'first_name' => 'Jane',
            'middle_name' => 'A',
            'birthdate' => '2001-05-15',
            'age' => 24,
            'sex' => 'Female',
            'address' => '456 Test Ave',
            'contact_no' => '09987654321',
            'course' => 'BSCS',
            'year_level' => '3rd Year',
            'status' => 'approved',
        ]);

        // Create a fake uploaded file
        $file = UploadedFile::fake()->image('document.pdf');
        $filePath = $file->store('private/patient_uploads', 'local');

        // Create a patient upload record
        $upload = PatientUpload::create([
            'student_account_id' => $student->id,
            'patient_id' => $patientInfo->id,
            'file_path' => $filePath,
            'original_name' => 'document.pdf',
            'file_size' => $file->getSize(),
        ]);

        // Verify the file exists
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Delete the upload record directly (not the student)
        $upload->delete();

        // Verify the physical file is also deleted
        $this->assertFalse(Storage::disk('local')->exists($filePath));

        // Verify the database record is deleted
        $this->assertDatabaseMissing('patient_uploads', [
            'id' => $upload->id,
        ]);

        // Verify the student still exists
        $this->assertDatabaseHas('student_accounts', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test that multiple uploads are all deleted when student is deleted.
     */
    public function test_student_deletion_removes_multiple_files(): void
    {
        // Fake the local storage (private)
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info for the student
        $patientInfo = PatientInfo::create([
            'student_account_id' => $student->id,
            'last_name' => 'Brown',
            'first_name' => 'Mike',
            'middle_name' => 'B',
            'birthdate' => '1999-12-25',
            'age' => 25,
            'sex' => 'Male',
            'address' => '789 Test Blvd',
            'contact_no' => '09111222333',
            'course' => 'BSBA',
            'year_level' => '2nd Year',
            'status' => 'approved',
        ]);

        // Create multiple uploads
        $filePaths = [];
        for ($i = 1; $i <= 3; $i++) {
            $file = UploadedFile::fake()->image("file{$i}.jpg");
            $filePath = $file->store('private/patient_uploads', 'local');
            $filePaths[] = $filePath;

            PatientUpload::create([
                'student_account_id' => $student->id,
                'patient_id' => $patientInfo->id,
                'file_path' => $filePath,
                'original_name' => "file{$i}.jpg",
                'file_size' => $file->getSize(),
            ]);

            // Verify each file exists
            $this->assertTrue(Storage::disk('local')->exists($filePath));
        }

        // Verify we have 3 uploads
        $this->assertEquals(3, $student->patientUploads()->count());

        // Delete the student
        $student->delete();

        // Verify all files are deleted
        foreach ($filePaths as $filePath) {
            $this->assertFalse(Storage::disk('local')->exists($filePath));
        }

        // Verify all database records are deleted
        $this->assertEquals(0, PatientUpload::where('student_account_id', $student->id)->count());
    }
}
