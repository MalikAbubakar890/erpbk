<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class visa_expenses extends Model
{
    use LogsActivity;

    protected $table = 'visa_expenses';

    protected $fillable = [
        'trans_date',
        'trans_code',
        'date',
        'rider_id',
        'visa_status',
        'detail',
        'billing_month',
        'amount',
        'payment_status',
    ];
    public static array $rules = [
        'trans_date' => 'nullable',
        'trans_code' => 'nullable',
        'date' => 'required',
        'rider_id' => 'nullable',
        'billing_month' => 'required',
        'visa_status' => 'nullable|string|max:50',
        'detail' => 'nullable|string|max:500',
        'amount' => 'required|numeric',
        'payment_status' => 'nullable|numeric',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];
    public function rider()
    {
        return $this->belongsTo(Riders::class, 'rider_id', 'id');
    }
    public function account()
    {
        return $this->belongsTo(Accounts::class, 'rider_id', 'id');
    }
    function transactions()
    {
        return $this->hasMany(Transactions::class, 'trans_code', 'trans_code');
    }
}
