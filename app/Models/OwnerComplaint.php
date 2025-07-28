<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerComplaint extends Model
{
    protected $fillable = [
        'owner_id',
        'membership_id',
        'complaint_text',
        'complaint_voice',
        'type',
        'user_assigned', 
        'user_assigned_by',
        'status', 
        'reply_text', 
        'replied_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationship to the Owner model
    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

       /**
     * Get the user (staff) assigned to the complaint.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'user_assigned');
    }

    /**
     * Get the user (staff) who assigned the complaint.
     */
    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'user_assigned_by');
    }

    /**
     * Get the user (staff) who replied to the complaint.
     */
    public function repliedByUser()
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}
