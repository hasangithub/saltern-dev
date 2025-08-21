<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollEarning extends Model
{
    protected $fillable = ['payroll_id','component_id','component_name','amount'];
    public function payroll(): BelongsTo { return $this->belongsTo(Payroll::class, 'payroll_id'); }
    public function component(): BelongsTo { return $this->belongsTo(PayrollComponent::class, 'component_id'); }
}
