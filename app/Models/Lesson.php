<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;

class Lesson extends Model
{
    use HasPrefixedId;

    protected $primaryKey = 'lesson_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'L';
    public const ID_PAD_LENGTH = 4;

    // If your table is named 'lessons' (default), no need to set $table.
    // protected $table = 'lessons';

    protected $fillable = [
        'tutor_id',
        'class_id',
        'day_of_week',   // 0..6 (Sun..Sat)
        'start_time',    // 'HH:MM:SS'
        'end_time',      // 'HH:MM:SS'
        'active_from',   // 'YYYY-MM-DD' | null
        'active_until',  // 'YYYY-MM-DD' | null
        'room',
    ];

    /* ── Relationships ───────────────────────────────────────────── */

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'tutor_id');
    }

    // Use Classroom model mapped to 'classes' table (recommended).
    public function classroom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'lesson_id', 'lesson_id');
    }

    public function scopeOrderedWeekly($query)
{
    return $query->orderBy('day_of_week')->orderBy('start_time');
}

    /* ── Recurrence helper: returns next date+time Carbon ────────── */

    public function nextOccurrence(Carbon $from = null): ?Carbon
    {
        $from = $from ?: Carbon::now();

        // Find the next matching weekday (0=Sun..6=Sat)
        $occurs = (clone $from)->startOfDay()->next($this->day_of_week);

        // Apply start time
        [$hS, $mS, $sS] = explode(':', $this->start_time);
        $occurs->setTime((int)$hS, (int)$mS, (int)$sS);

        // If already passed for this week, move ahead one week
        if ($occurs->lessThanOrEqualTo($from)) {
            $occurs->addWeek();
        }

        // Respect active window (if set)
        if ($this->active_from && $occurs->lt(Carbon::parse($this->active_from))) {
            $start = Carbon::parse($this->active_from);
            $occurs = $start->copy()->startOfDay()->next($this->day_of_week)
                ->setTime((int)$hS, (int)$mS, (int)$sS);
        }
        if ($this->active_until && $occurs->gt(Carbon::parse($this->active_until)->endOfDay())) {
            return null; // no more future lessons in window
        }

        return $occurs;
    }
}
