<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    protected $table = 'payrolls';
    protected $fillable = [
        'batch_id','employee_id', 'basic_salary', 'day_salary', 'worked_days','overtime_hours', 'overtime_amount', 'no_pay','no_pay_days',
        'mercantile_days',
        'mercantile_days_amount',
        'extra_full_days',
        'extra_full_days_amount',
        'extra_half_days',
        'extra_half_days_amount',
        'eight_hours_duty_hours',
        'eight_hours_duty_amount',
        'poovarasan_kuda_allowance_150',
        'poovarasan_kuda_allowance_150_amount',
        'labour_hours',
        'labour_amount',
        'epf_employee' , 'epf_employer', 'etf','gross_earnings','total_deductions','net_pay','status','payslip_path'
    ];

    public function batch(): BelongsTo { return $this->belongsTo(PayrollBatch::class, 'batch_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function earnings(): HasMany { return $this->hasMany(PayrollEarning::class, 'payroll_id'); }
    public function deductions(): HasMany { return $this->hasMany(PayrollDeduction::class, 'payroll_id'); }
}
