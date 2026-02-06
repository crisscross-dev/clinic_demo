<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Consultation;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read string $full_name
 */
class PatientInfo extends Model
{

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // When a patient info is being deleted, delete all associated uploaded files
        static::deleting(function (PatientInfo $patientInfo) {
            // Get all patient uploads for this patient
            $uploads = $patientInfo->patientUploads;

            foreach ($uploads as $upload) {
                // Delete the physical file from private storage
                if ($upload->file_path && Storage::disk('local')->exists($upload->file_path)) {
                    Storage::disk('local')->delete($upload->file_path);

                    // Delete empty parent directories
                    static::deleteEmptyDirectories($upload->file_path);
                }

                // Delete the database record
                $upload->delete();
            }
        });
    }

    /**
     * Delete empty parent directories after file deletion.
     *
     * @param string $filePath The file path that was deleted
     * @return void
     */
    protected static function deleteEmptyDirectories(string $filePath): void
    {
        $disk = Storage::disk('local');
        $directory = dirname($filePath);

        // Keep deleting empty parent directories until we hit a non-empty one or the base path
        while ($directory && $directory !== '.' && $directory !== 'private/patient_uploads') {
            // Get all files and directories in this directory
            $contents = $disk->allFiles($directory);
            $subdirectories = $disk->directories($directory);

            // If directory is empty (no files and no subdirectories), delete it
            if (empty($contents) && empty($subdirectories)) {
                $disk->deleteDirectory($directory);
                // Move up to parent directory
                $directory = dirname($directory);
            } else {
                // Directory is not empty, stop here
                break;
            }
        }
    }
    protected $fillable = [
        // Foreign key
        'student_account_id',
        // Identity & demographics
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'sex',
        'age',
        'birthdate',
        'nationality',
        'religion',
        // Contact & address
        'contact_no',
        'address',
        // School-related
        'department',
        'course',
        'year_level',
        // Emergency / guardian
        'father_name',
        'mother_name',
        'guardian_name',
        'guardian_relationship',
        'father_contact_no',
        'mother_contact_no',
        'guardian_contact_no',
        'guardian_address',
        // Medical info
        'allergies',
        'other_allergies',
        'treatments',
        'covid',
        'flu_vaccine',
        'other_vaccine',
        'medical_history',
        'medication',
        'lasthospitalization',
        'consent',
        'consent_by',
        'consent_form',
        'consent_access_requested',
        'signature',
        // Status
        'status',
    ];


    // Automatically format name fields before saving
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            foreach (
                [
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                    'nationality',
                    'religion',

                    'father_name',
                    'mother_name',
                    'guardian_name',
                    'guardian_relationship',
                    'guardian_address',

                    'consent_by',
                ] as $field
            ) {
                if (!empty($model->$field)) {
                    $model->$field = ucwords(strtolower(trim($model->$field)));
                }
            }
        });
    }

    /**
     * Get the patient's full name.
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->last_name . ', ' . $this->first_name;

        if (!empty($this->middle_name)) {
            $name .= ' ' . substr($this->middle_name, 0, 1) . '.';
        }

        return $name;
    }

    protected $casts = [
        'birthdate' => 'date',
        'consent_form' => 'boolean',
        'consent_access_requested' => 'boolean',
    ];

    /**
     * Student account relationship.
     */
    public function studentAccount()
    {
        return $this->belongsTo(StudentAccount::class);
    }

    /**
     * Consultations relationship.
     */
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    /**
     * Patient uploads relationship.
     */
    public function patientUploads()
    {
        return $this->hasMany(PatientUpload::class, 'patient_id');
    }

    /**
     * Consent requests relationship.
     */
    public function consentRequests()
    {
        return $this->hasMany(PatientConsent::class, 'patient_info_id');
    }

    /**
     * Get or create a patient record for a student.
     * This ensures one patient record per student (student as parent).
     */
    public static function getOrCreateForStudent($studentId)
    {
        return static::firstOrCreate(
            ['student_account_id' => $studentId],
            ['status' => 'pending']
        );
    }

    /**
     * Update or create patient info for a student.
     */
    public static function updateOrCreateForStudent($studentId, array $data)
    {
        return static::updateOrCreate(
            ['student_account_id' => $studentId],
            array_merge($data, ['status' => 'pending'])
        );
    }

    /**
     * Calculate profile completion percentage.
     * Returns percentage of non-empty required and optional fields.
     */
    public function getCompletionPercentageAttribute(): int
    {
        // Define required fields (most important)
        $requiredFields = [
            'first_name',
            'last_name',
            'course',
            'department',
            'sex',
            'age',
            'contact_no',
            'address'
        ];

        // Define optional but important fields
        $optionalFields = [
            'middle_name',
            'birthdate',
            'nationality',
            'religion',
            'year_level',
            'father_name',
            'mother_name',
            'guardian_name',
            'guardian_contact_no',
            'allergies',
            'medical_history',
            'signature'
        ];

        $totalFields = count($requiredFields) + count($optionalFields);
        $filledFields = 0;

        // Count filled required fields (weight them more)
        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $filledFields += 1.5; // Required fields count as 1.5
            }
        }

        // Count filled optional fields
        foreach ($optionalFields as $field) {
            if (!empty($this->$field)) {
                $filledFields += 1;
            }
        }

        // Adjust total to account for weighted required fields
        $adjustedTotal = (count($requiredFields) * 1.5) + count($optionalFields);

        return min(100, round(($filledFields / $adjustedTotal) * 100));
    }

    /**
     * Get completion status with color coding.
     */
    public function getCompletionStatusAttribute(): array
    {
        $percentage = $this->completion_percentage;

        if ($percentage >= 90) {
            return ['text' => 'Excellent', 'color' => 'success', 'percentage' => $percentage];
        } elseif ($percentage >= 70) {
            return ['text' => 'Good', 'color' => 'primary', 'percentage' => $percentage];
        } elseif ($percentage >= 50) {
            return ['text' => 'Fair', 'color' => 'warning', 'percentage' => $percentage];
        } else {
            return ['text' => 'Incomplete', 'color' => 'danger', 'percentage' => $percentage];
        }
    }
}
