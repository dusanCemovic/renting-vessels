<?php

namespace App\Services;

use App\Models\Maintenance;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

final class Repository
{

    /**
     * @return Collection
     */
    public static function getReservations() : Collection
    {
        $reservations = Reservation::with('vessel')->get();
        return ($reservations->map(function ($r) {
            return (object) [
                'id' => 'res:'.$r->id,
                'model' => 'reservation',
                'title' => $r->title,
                'vessel_id' => $r->vessel_id,
                'vessel_name' => optional($r->vessel)->name,
                'start_at' => $r->start_at,
                'end_at' => $r->end_at,
                'type' => 'Reservation',
                'status' => $r->status ?? 'scheduled',
            ];
        }));
    }

    /**
     * @return Collection
     */
    public static function getMaintenances()
    {
        $maintenances = Maintenance::with('vessel')->get();
        return ($maintenances->map(function ($m) {
            return (object) [
                'id' => 'mnt:'.$m->id,
                'model' => 'maintenance',
                'title' => $m->title,
                'vessel_id' => $m->vessel_id,
                'vessel_name' => optional($m->vessel)->name,
                'start_at' => $m->start_at,
                'end_at' => $m->end_at,
                'type' => 'Maintenance',
                'status' => 'scheduled',
            ];
        }));
    }

    /**
     * @param Collection $tasks
     * @param string $sort
     * @param string $dir
     * @return Collection
     */
    public static function sort(Collection $tasks, string $sort, string $dir): Collection
    {
        // Sorting
        $key = match ($sort) {
            'vessel' => 'vessel_name',
            'end' => 'end_at',
            'type' => 'type',
            default => 'start_at',
        };

        return $dir === 'asc' ? $tasks->sortBy($key)->values() : $tasks->sortByDesc($key)->values();
    }

    /**
     * This can be used to leave always UTC in db but use local on front
     *
     * @param string $date
     * @return Carbon|string
     */
    public static function dateFromLocalToDB(string $date, bool $withFormat = false, string $format = 'Y-m-d H:i:s')
    {
        $date = Carbon::parse($date, 'Europe/Ljubljana');
        $dbPrepared = $date->copy()->setTimezone('UTC');

        if ($withFormat) {
            return $dbPrepared->format($format);
        } else {
            return $dbPrepared;
        }

    }
}
