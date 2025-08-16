<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['city', 'netrange', 'cidr', 'netname', 'organization', 'country', 'admin_name', 'admin_email', 'tech_name', 'tech_email', 'remarks'];

    public function ips(): HasMany { return $this->hasMany(Ip::class); }
}
