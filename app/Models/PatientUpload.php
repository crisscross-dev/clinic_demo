<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PatientUpload extends Model
{
    use HasFactory;

    // Tell Laravel the table name (optional if it matches naming convention)
    protected $table = 'patient_uploads';

    // Fields that can be mass assigned
    protected $fillable = [
        'patient_id',
        'student_account_id',
        'file_path',
        'original_name',
        'file_size',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // When a patient upload is being deleted, delete the physical file
        static::deleting(function (PatientUpload $upload) {
            // Delete the physical file from private storage (storage/app/private/patient_uploads)
            if ($upload->file_path && Storage::disk('local')->exists($upload->file_path)) {
                Storage::disk('local')->delete($upload->file_path);

                // Delete empty parent directories
                static::deleteEmptyDirectories($upload->file_path);
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

    // Each upload belongs to one patient
    public function patient()
    {
        return $this->belongsTo(PatientInfo::class, 'patient_id');
    }

    // Each upload belongs to one student account
    public function studentAccount()
    {
        return $this->belongsTo(StudentAccount::class);
    }
}
