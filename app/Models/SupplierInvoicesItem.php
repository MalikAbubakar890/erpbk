<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoicesItem extends Model
{
    protected $table = 'supplier_invoice_items';
    protected $fillable = [
        'inv_id', 'item_id', 'item_des', 'qty', 'rate', 'discount', 'tax', 'amount',
    ];

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoices::class, 'inv_id');
    }

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }
}
