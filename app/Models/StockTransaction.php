<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',       // restock | deduct | dispensed
        'quantity',
        'admin_id',
        'consultation_id',  // Reference to consultation for medicine dispensing
        'notes',      // Additional notes (reason, patient info, etc.)
    ];

    /**
     * Relation: Each transaction belongs to an inventory item
     */
    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    /**
     * Relation: Each transaction may belong to a consultation (for dispensed medicines)
     */
    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }

    /**
     * Relation: (Optional) Each transaction may be performed by a user (staff/admin)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation: Each transaction is performed by an admin
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
