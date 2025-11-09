<?php

namespace App\Services;

use App\Models\Vessel;
use Carbon\Carbon;

final class VesselReservation
{

    public static function getVesselsWithEquipment($required)
    {
//        return Vessel::with(['equipment', 'reservations', 'maintenances'])
        return Vessel::with('equipment')
            ->get()
            ->filter(function ($ves) use ($required) {
                $have = $ves->equipment->pluck('code')->toArray();
                return empty(array_diff($required, $have));
            });
    }

    /**
     * Determine which vessels are available for the given interval.
     *
     */
    public static function checkAvailability($vessels, $start, $end) : array
    {
        $available = array();

        foreach ($vessels as $itemVessel) {

            if (! self::checkTerms($itemVessel?->reservations, $start, $end)) {
                continue;
            }

            if (! self::checkTerms($itemVessel?->maintenances, $start, $end)) {
                continue;
            }

            // Add to available if no conflict
            $available[] = $itemVessel;

        }

        return self::orderPerPriority($available);
    }

    /**
     * Suggest the earliest availability for each vessel after now.
     *
     */
    public static function getSuggestions($vessels, $start, $end): array
    {
        $suggestions = [];

        foreach ($vessels as $itemVessel) {
            // collect all task maintenances
            $now = Carbon::now();

            // collect all tasks and maintenances
            $terms = self::getAllReservationsAndMaintenances($itemVessel, $now);

            $suggestions[] = self::getSuggestionsBasedOnTasks($itemVessel, $terms, $start, $end, $now);
        }

        return self::orderPerTime($suggestions);
    }

    /**
     * This funciton is used to check together maintenance and reservations
     * @param mixed $terms
     * @param $start
     * @param $end
     * @return bool
     */
    public static function checkTerms(mixed $terms, $start, $end)
    {
        // Check reservation conflicts
        foreach ($terms as $term) {
            // allow that new reservation/maintenance start when other is finished
            if (
                (($term->start_at >= $start && $term->start_at <= $end) ||
                    ($term->end_at > $start && $term->end_at <= $end)) ||
                ($term->start_at < $start && $term->end_at > $end)
            ) {
                return false;
            }
        }

        return true;

    }

    /**
     * Sort giving vessels per priority. Maybe it is better to give vessel with:
     * as less equipment as possible (we will use this example)
     * or smaller size
     * etc
     * @param array $possibilities
     * @return array
     */
    public static function orderPerPriority(array $possibilities): array
    {
        // If the 'equipment' relation is loaded, use its in-memory count.
        // Otherwise, query-count via relation without loading the collection.

        usort($possibilities, function ($a, $b) {
            $count = function ($v) {
                try {
                    if (method_exists($v, 'relationLoaded') && $v->relationLoaded('equipment')) {
                        return $v->equipment ? $v->equipment->count() : 0;
                    }
                    if (method_exists($v, 'equipment')) {
                        return (int) $v->equipment()->count();
                    }
                } catch (\Throwable $_) {
                    // ignore and fall back to 0
                }
                return 0;
            };

            $aCount = $count($a);
            $bCount = $count($b);

            if ($aCount !== $bCount) {
                return $aCount <=> $bCount; // fewer equipment first
            }

            // Tie-breakers to keep order stable
            return $a->id <=> $b->id;
        });

        return array_values($possibilities);
    }

    /**
     * @param mixed $itemVessel
     * @param Carbon $now
     * @return array
     */
    public static function getAllReservationsAndMaintenances(mixed $itemVessel, Carbon $now): array
    {
        $busy = [];

        // reservations
        foreach ($itemVessel->reservations as $res) {
            if ($res->end_at >= $now) {
                $busy[] = array(
                    'start' => Carbon::parse($res->start_at),
                    'end' => Carbon::parse($res->end_at),
                );
            }
        }

        // maintenances
        foreach ($itemVessel->maintenances as $mnt) {
            if ($mnt->end_at >= $now) {
                $busy[] = array(
                    'start' => Carbon::parse($mnt->start_at),
                    'end' => Carbon::parse($mnt->end_at),
                );
            }
        }

        // sort them
        usort($busy, function ($a, $b) {
            if ($a['start'] == $b['start']) return 0;
            return ($a['start'] < $b['start']) ? -1 : 1;
        });

        return array_values($busy);
    }

    public static function getSuggestionsBasedOnTasks($itemVessel, $terms, $start, $end, $now) : array
    {
        // find first slot where a [start,end] interval fits
        $tryStart = $start->copy();

        // just set now if start is in the past
        if ($tryStart->lt($now)) $tryStart = $now->copy();

        $found = null;

        // iterate busy intervals and try to put inside of them
        foreach ($terms as $interval) {
            // if we find before
            if ($tryStart->copy()->lte($interval['start'])) {
                $tryEnd = $tryStart->copy()->addSeconds(abs($end->diffInSeconds($start)));
                if ($tryEnd->lte($interval['start'])) {
                    $found = $tryStart->copy();
                    break;
                }
            }
            // move $tryStart to after interval end if it overlaps, we are going to next space
            if ($tryStart->lt($interval['end'])) {
                $tryStart = $interval['end']->copy();
            }
        }

        if (!$found) {
            // if no busy intervals prevent it, schedule at candidateStart (after last busy interval)
            $found = $tryStart->copy();
        }

        return [
            'vessel_id' => $itemVessel->id,
            'vessel_name' => $itemVessel->name,
            'available_from' => $found->toIso8601String()
        ];
    }

    /**
     * First to order per time. If we have same vessels availlable on the same time, we will use priority also
     * @param array $suggestions
     * @return array
     */
    public static function orderPerTime(array $suggestions) : array
    {
        usort($suggestions, function ($a, $b) {
            return strcmp($a['available_from'], $b['available_from']);
        });

        return $suggestions;
    }
}
