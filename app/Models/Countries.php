<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
  use HasFactory;
  public $table = 'countries';

  public static function list()
  {
    return self::all()->pluck('name', 'id');
  }
  public static function phonecode()
  {
    return self::all()->pluck('code', 'id');
  }

}
