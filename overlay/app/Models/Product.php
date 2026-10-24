<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'sku', 'description', 'price_cents', 'stock'];
    protected function casts(): array { return ['price_cents' => 'integer', 'stock' => 'integer']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
