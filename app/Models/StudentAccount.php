<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;

class StudentAccount extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, CanResetPasswordTrait;

    /**
     * Send the password reset notification using the custom notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Automatically delete related uploads when deleting a student.
     */
    protected static function booted(): void
    {
        static::deleting(function (StudentAccount $student) {
            foreach ($student->patientUploads as $upload) {
                if ($upload->file_path && Storage::disk('local')->exists($upload->file_path)) {
                    Storage::disk('local')->delete($upload->file_path);
                    static::deleteEmptyDirectories($upload->file_path);
                }
                $upload->delete();
            }
        });
    }

    /**
     * Delete empty parent directories after file deletion.
     */
    protected static function deleteEmptyDirectories(string $filePath): void
    {
        $disk = Storage::disk('local');
        $directory = dirname($filePath);

        while ($directory && $directory !== '.' && $directory !== 'private/patient_uploads') {
            $contents = $disk->allFiles($directory);
            $subdirectories = $disk->directories($directory);

            if (empty($contents) && empty($subdirectories)) {
                $disk->deleteDirectory($directory);
                $directory = dirname($directory);
            } else {
                break;
            }
        }
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'password',
        'last_login_at',
        'status',
        'privacy_policy',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'privacy_policy' => 'boolean',
    ];

    /**
     * Get the student's full name from patient info.
     */
    public function getFullNameAttribute(): string
    {
        return $this->patientInfo->full_name ?? 'No patient info available';
    }

    /**
     * Relationships
     */
    public function patientInfo(): HasOne
    {
        return $this->hasOne(PatientInfo::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function patientUploads(): HasMany
    {
        return $this->hasMany(PatientUpload::class);
    }



    public function getLastLoginHumanAttribute()
    {
        return $this->last_login_at
            ? Carbon::parse($this->last_login_at)->diffForHumans()
            : 'Never logged in';
    }

    /**
     * Scope to filter by email.
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }
}
