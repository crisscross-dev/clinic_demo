<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientConsent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'patient_consent';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_info_id',
        'consent_reason',
        'status',
    ];

    /**
     * Get the patient info that owns the consent request.
     */
    public function patientInfo()
    {
        return $this->belongsTo(PatientInfo::class, 'patient_info_id');
    }
}
