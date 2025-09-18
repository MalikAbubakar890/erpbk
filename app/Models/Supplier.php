<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Accounts;

class Supplier extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'email',
    'phone',
    'company_name',
    'address',
    'account_id',
    'status'
  ];
  public function account()
  {
    return $this->belongsTo(Accounts::class, 'account_id');
  }
  public static function dropdown()
 { 
    return self::select('id', \DB::raw("CONCAT(id, '-', name) as full_name"))
        ->pluck('full_name', 'id')
        ->prepend('Select', '');
 } 
}
