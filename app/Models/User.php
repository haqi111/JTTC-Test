<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Contract;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   
    use HasFactory, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded =[] ;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }


    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getWorkingDurationAttribute()
    {
        if (!$this->join_date) {
            return null;
        }
    
        $start = Carbon::parse($this->join_date);
        $lastContractEnd = optional($this->contracts()
            ->orderByDesc('end_date')
            ->first())->end_date;
    
        if ($lastContractEnd && Carbon::parse($lastContractEnd)->isPast()) {
            $end = Carbon::parse($lastContractEnd);
        } else {
            $end = Carbon::now();
        }
    
        $diff = $start->diff($end);
    
        return sprintf('%d Tahun %d Bulan %d Hari', $diff->y, $diff->m, $diff->d);
    }

    public function getRemainingContractDaysAttribute(): ?string
    {
        $latestEndDate = $this->contracts()
            ->orderByDesc('end_date')
            ->value('end_date');

        if (!$latestEndDate) {
            return null;
        }

        $now = \Carbon\Carbon::now();
        $endDate = \Carbon\Carbon::parse($latestEndDate);

        if ($now->greaterThan($endDate)) {
            return '🔴 Expired';
        }

        
        $diffInDays = floor($now->diffInRealDays($endDate)); 

        return match (true) {
            $endDate->isPast() => "🔴 Expired",
            $diffInDays <= 7 => "🔴 {$diffInDays} Hari ",
            $diffInDays <= 30 => "🟡 {$diffInDays} Hari ",
            default => "🟢 {$diffInDays} Hari ",
        };
        
    }

    protected static function booted(): void
    {
        static::creating(function ($user) {
            if ($user->nip) {
                return;
            }

            if (!$user->division_id || !$user->join_date) {
                return;
            }

            $divisionCode = optional($user->division)->division_code;
            $joinDate = \Carbon\Carbon::parse($user->join_date);
            $monthYear = $joinDate->format('my');

            $nipPrefix = "{$divisionCode}{$monthYear}";

            $lastNip = User::orderByDesc('nip')->value('nip');
            $lastNumber = $lastNip ? intval(substr($lastNip, -4)) : 0;
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            $user->nip = "{$nipPrefix}{$nextNumber}";
        });
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
