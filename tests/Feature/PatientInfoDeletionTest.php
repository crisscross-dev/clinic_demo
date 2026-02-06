<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StudentAccount;
use App\Models\PatientInfo;
use App\Models\PatientUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PatientInfoDeletionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that when a patient info is deleted, 
     * their uploaded files are also deleted from storage.
     */
    public function test_patient_info_deletion_removes_uploaded_files(): void
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
            'patient_id' => $patientInfo->id,
        ]);

        // Delete the patient info
        $patientInfo->delete();

        // Verify the database record is deleted
        $this->assertDatabaseMissing('patient_uploads', [
            'id' => $upload->id,
        ]);

        // Verify the physical file is deleted from storage
        $this->assertFalse(Storage::disk('local')->exists($filePath));

        // Verify the student still exists
        $this->assertDatabaseHas('student_accounts', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test that multiple uploads are all deleted when patient info is deleted.
     */
    public function test_patient_info_deletion_removes_multiple_files(): void
    {
        // Fake the local storage
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

        // Create multiple uploads in private/patient_uploads folder
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
        $this->assertEquals(3, $patientInfo->patientUploads()->count());

        // Delete the patient info
        $patientInfo->delete();

        // Verify all files are deleted
        foreach ($filePaths as $filePath) {
            $this->assertFalse(Storage::disk('local')->exists($filePath));
        }

        // Verify all database records are deleted
        $this->assertEquals(0, PatientUpload::where('patient_id', $patientInfo->id)->count());

        // Verify the student still exists
        $this->assertDatabaseHas('student_accounts', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test that files in nested folders within private/patient_uploads are deleted.
     */
    public function test_patient_info_deletion_removes_files_from_nested_folders(): void
    {
        // Fake the local storage
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info
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

        // Create uploads in different nested folders
        $filePaths = [
            'private/patient_uploads/2025/october/document1.pdf',
            'private/patient_uploads/2025/october/document2.pdf',
            'private/patient_uploads/medical_records/xray.jpg',
        ];

        foreach ($filePaths as $filePath) {
            // Create the file
            Storage::disk('local')->put($filePath, 'test content');

            // Create database record
            PatientUpload::create([
                'student_account_id' => $student->id,
                'patient_id' => $patientInfo->id,
                'file_path' => $filePath,
                'original_name' => basename($filePath),
                'file_size' => 12,
            ]);

            // Verify file exists
            $this->assertTrue(Storage::disk('local')->exists($filePath));
        }

        // Verify the nested directories exist
        $this->assertTrue(Storage::disk('local')->exists('private/patient_uploads/2025/october'));
        $this->assertTrue(Storage::disk('local')->exists('private/patient_uploads/medical_records'));

        // Delete the patient info
        $patientInfo->delete();

        // Verify all files are deleted regardless of folder structure
        foreach ($filePaths as $filePath) {
            $this->assertFalse(Storage::disk('local')->exists($filePath));
        }

        // Verify empty nested directories are also deleted
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/2025/october'));
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/2025'));
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/medical_records'));
    }

    /**
     * Test cascade deletion: Student -> PatientInfo -> PatientUploads -> Physical Files
     */
    public function test_student_deletion_cascades_to_patient_info_and_uploads(): void
    {
        // Fake the local storage
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info
        $patientInfo = PatientInfo::create([
            'student_account_id' => $student->id,
            'last_name' => 'Taylor',
            'first_name' => 'Sarah',
            'middle_name' => 'L',
            'birthdate' => '2002-03-10',
            'age' => 23,
            'sex' => 'Female',
            'address' => '321 Test Rd',
            'contact_no' => '09222333444',
            'course' => 'BSEE',
            'year_level' => '1st Year',
            'status' => 'approved',
        ]);

        // Create uploads
        $file = UploadedFile::fake()->image('profile.jpg');
        $filePath = $file->store('private/patient_uploads', 'local');

        PatientUpload::create([
            'student_account_id' => $student->id,
            'patient_id' => $patientInfo->id,
            'file_path' => $filePath,
            'original_name' => 'profile.jpg',
            'file_size' => $file->getSize(),
        ]);

        // Verify everything exists
        $this->assertTrue(Storage::disk('local')->exists($filePath));
        $this->assertDatabaseHas('patient_infos', ['id' => $patientInfo->id]);
        $this->assertDatabaseHas('patient_uploads', ['patient_id' => $patientInfo->id]);

        // Delete the student (should cascade to patient_info and uploads)
        $student->delete();

        // Verify everything is deleted
        $this->assertFalse(Storage::disk('local')->exists($filePath));
        $this->assertDatabaseMissing('patient_infos', ['id' => $patientInfo->id]);
        $this->assertDatabaseMissing('patient_uploads', ['patient_id' => $patientInfo->id]);
    }

    /**
     * Test that deleting a single upload also removes empty directories.
     */
    public function test_individual_upload_deletion_removes_empty_directories(): void
    {
        // Fake the local storage
        Storage::fake('local');

        // Create a student account
        $student = StudentAccount::create([
            'email' => 'test5@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create patient info
        $patientInfo = PatientInfo::create([
            'student_account_id' => $student->id,
            'last_name' => 'Wilson',
            'first_name' => 'Tom',
            'middle_name' => 'K',
            'birthdate' => '2000-06-20',
            'age' => 25,
            'sex' => 'Male',
            'address' => '555 Test Lane',
            'contact_no' => '09333444555',
            'course' => 'BSME',
            'year_level' => '4th Year',
            'status' => 'approved',
        ]);

        // Create an upload in a nested directory
        $file = UploadedFile::fake()->image('xray.jpg');
        $filePath = $file->store('private/patient_uploads/2025/october/xrays', 'local');

        $upload = PatientUpload::create([
            'student_account_id' => $student->id,
            'patient_id' => $patientInfo->id,
            'file_path' => $filePath,
            'original_name' => 'xray.jpg',
            'file_size' => $file->getSize(),
        ]);

        // Verify the file and directories exist
        $this->assertTrue(Storage::disk('local')->exists($filePath));
        $this->assertTrue(Storage::disk('local')->exists('private/patient_uploads/2025/october/xrays'));
        $this->assertTrue(Storage::disk('local')->exists('private/patient_uploads/2025/october'));
        $this->assertTrue(Storage::disk('local')->exists('private/patient_uploads/2025'));

        // Delete the upload
        $upload->delete();

        // Verify the file is deleted
        $this->assertFalse(Storage::disk('local')->exists($filePath));

        // Verify empty directories are also deleted
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/2025/october/xrays'));
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/2025/october'));
        $this->assertFalse(Storage::disk('local')->exists('private/patient_uploads/2025'));

        // Verify patient info and student still exist
        $this->assertDatabaseHas('patient_infos', ['id' => $patientInfo->id]);
        $this->assertDatabaseHas('student_accounts', ['id' => $student->id]);
    }
}
