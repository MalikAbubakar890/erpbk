<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
  protected $fillable = [
    'account_id',
    'billing_month',
    'debit_balance',
    'credit_balance',
    'opening_balance',
    'closing_balance'
  ];

  public function account()
  {
    return $this->belongsTo(Accounts::class);
  }
}


?>