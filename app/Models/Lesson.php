<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasPrefixedId;
use App\Models\Tutor;
use App\Models\Classes;      // table `classes`
use App\Models\Attendance;
use App\Models\Student;      // table `students`

class Lesson extends Model
{
    use HasPrefixedId;

    protected $table = 'lessons';
    protected $primaryKey = 'lesson_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public const ID_PREFIX = 'L';
    public const ID_PAD_LENGTH = 4;

    // 约定：day_of_week 使用 0..6（0=Sun...6=Sat）
    protected $fillable = [
        'tutor_id',
        'class_id',
        'day_of_week',   // 0..6
        'start_time',    // 'HH:MM:SS'
        'end_time',      // 'HH:MM:SS'
        'active_from',   // 'YYYY-MM-DD' | null
        'active_until',  // 'YYYY-MM-DD' | null
        'room',
    ];

    // ✅ 让 Resource 能正确处理日期；旧代码不受影响
    protected $casts = [
        'day_of_week'  => 'integer',
        'active_from'  => 'date:Y-m-d',
        'active_until' => 'date:Y-m-d',
    ];

    /* ── Relationships ───────────────────────────────────────────── */

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'tutor_id');
    }

    // 兼容旧代码：你之前用的是 classroom()
    public function classroom()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    // 新增：标准关系名 class()，供 with('class') / Resource 使用
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'lesson_id', 'lesson_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'lesson_students', // pivot
            'lesson_id',
            'student_id'
        )->withTimestamps();
    }

    /* ── Scopes ──────────────────────────────────────────────────── */

    public function scopeOrderedWeekly($query)
    {
        return $query->orderBy('day_of_week')->orderBy('start_time');
    }

    public function scopeActiveOn($query, $date /* YYYY-MM-DD */)
    {
        return $query
            ->where(fn($q) => $q->whereNull('active_from')->orWhere('active_from', '<=', $date))
            ->where(fn($q) => $q->whereNull('active_until')->orWhere('active_until', '>=', $date));
    }

    public function scopeTimeWindow($query, ?string $from = null, ?string $to = null)
    {
        if ($from && $to) return $query->whereBetween('start_time', [$from, $to]);
        if ($from)        return $query->where('start_time', '>=', $from);
        if ($to)          return $query->where('start_time', '<=', $to);
        return $query;
    }

    /* ── Helpers ─────────────────────────────────────────────────── */

    public function nextOccurrence(Carbon $from = null): ?Carbon
    {
        $from = $from ?: Carbon::now();

        $target = (int) $this->day_of_week;   // 0..6
        $today  = (int) $from->dayOfWeek;     // 0..6

        $daysToAdd = ($target - $today + 7) % 7;

        [$hS, $mS, $sS] = $this->explodeHms((string) $this->start_time);
        $occurs = (clone $from)->startOfDay()->addDays($daysToAdd)->setTime($hS, $mS, $sS);

        if ($daysToAdd === 0 && $occurs->lessThanOrEqualTo($from)) {
            $occurs->addWeek();
        }

        if ($this->active_from) {
            $af = Carbon::parse($this->active_from)->startOfDay();
            if ($occurs->lt($af)) {
                $afDow = (int) $af->dayOfWeek;
                $shift = ($target - $afDow + 7) % 7;
                $occurs = (clone $af)->addDays($shift)->setTime($hS, $mS, $sS);
            }
        }
        if ($this->active_until) {
            $auEnd = Carbon::parse($this->active_until)->endOfDay();
            if ($occurs->gt($auEnd)) return null;
        }

        return $occurs;
    }

    protected function explodeHms(string $time): array
    {
        $p = explode(':', $time);
        return [(int)($p[0] ?? 0), (int)($p[1] ?? 0), (int)($p[2] ?? 0)];
    }

    public function getRouteKeyName(): string
    {
        return 'lesson_id';
    }
}
