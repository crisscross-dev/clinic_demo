<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class Consultation extends Model
{
    use HasFactory;
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'admin_id',
        'student_account_id',
        'assessed_by',
        'chief_complaint',
        'temperature',
        'blood_pressure',
        'pulse_rate',
        'respiratory_rate',
        'spo2',
        'lmp',
        'pain_scale',
        'assessment',
        'intervention',
        'outcome',
        'dispensed_medicines',
    ];

    protected $casts = [
        'lmp' => 'date',
        'temperature' => 'decimal:1',
        'pulse_rate' => 'integer',
        'respiratory_rate' => 'integer',
        'spo2' => 'integer',
        'dispensed_medicines' => 'array',
    ];

    public function setChiefComplaintAttribute($value)
    {
        $this->attributes['chief_complaint'] = strtoupper(trim($value));
    }


    public function patient()
    {
        return $this->belongsTo(PatientInfo::class, 'patient_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function studentAccount()
    {
        return $this->belongsTo(StudentAccount::class);
    }
 
    /**
     * Get formatted dispensed medicines string
     */
    public function getDispensedMedicinesListAttribute()
    {
        if (empty($this->dispensed_medicines)) {
            return 'No medicines dispensed';
        }

        $medicines = [];
        foreach ($this->dispensed_medicines as $medicine) {
            $instruction = !empty($medicine['instructions']) ? ' - ' . $medicine['instructions'] : '';
            $medicines[] = "{$medicine['quantity']} {$medicine['name']}{$instruction}";
        }

        return implode('; ', $medicines);
    }

    /**
     * Check if any medicines were dispensed
     */
    public function hasMedicinesDispensed()
    {
        return !empty($this->dispensed_medicines);
    }

    /**
     * Relation: Get stock transactions for medicines dispensed in this consultation
     */
    public function medicineTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'consultation_id')
            ->where('type', 'dispensed');
    }

    /**
     * Get dispensed medicines from stock transactions instead of JSON field
     */
    public function getDispensedMedicinesFromTransactions()
    {
        return $this->medicineTransactions()
            ->with('item')
            ->get()
            ->map(function ($transaction) {
                return [
                    'item_id' => $transaction->item_id, // Add item_id for proper grouping
                    'name' => $transaction->item->name,
                    'quantity' => $transaction->quantity,
                    'dispensed_at' => $transaction->created_at,
                ];
            });
    }
}
