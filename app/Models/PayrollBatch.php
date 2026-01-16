<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollBatch extends Model
{
    protected $fillable = ['pay_period','batch_type', 'payroll_template_id','status','processed_by','processed_by'];

    public function payrolls(): HasMany {
        return $this->hasMany(Payroll::class, 'batch_id');
    }

    public function payrollTemplate()
    {
        return $this->belongsTo(PayrollTemplate::class);
    }
}
