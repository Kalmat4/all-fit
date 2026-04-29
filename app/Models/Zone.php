<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    protected $fillable = [
        'user_id',
        'oblast_name',
        'bbox_west',
        'bbox_south',
        'bbox_east',
        'bbox_north',
    ];

    protected function casts(): array
    {
        return [
            'bbox_west'  => 'float',
            'bbox_south' => 'float',
            'bbox_east'  => 'float',
            'bbox_north' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
