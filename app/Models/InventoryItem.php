<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;
    protected $fillable = ['item_name', 'category', 'total_quantity', 'unit_price', 'description'];
}