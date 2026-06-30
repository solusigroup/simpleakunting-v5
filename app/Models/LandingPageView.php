<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageView extends Model
{
    use HasFactory;

    protected $table = 'landing_page_views';

    protected $fillable = [
        'ip_address',
        'user_agent',
    ];
}
