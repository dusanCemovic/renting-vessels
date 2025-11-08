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
    public static function checkAvailability($vessels, $start, $end)
    {
        $available = array();

        foreach ($vessels as $itemVessel) {
            $conflictTasks = false;
            $conflictMaintenance = false;

            // Check reservation conflicts (all in UTC)
            foreach ($itemVessel->reservations as $res) {
                if (
                    (($res->start_at >= $start && $res->start_at <= $end) ||
                        ($res->end_at >= $start && $res->end_at <= $end)) ||
                    ($res->start_at < $start && $res->end_at > $end)
                ) {
                    $conflictTasks = true;
                    break;
                }
            }

            // Check maintenance conflicts (all in UTC)
            foreach ($itemVessel->maintenances as $mtn) {
                if (
                    (($mtn->start_at >= $start && $mtn->start_at <= $end) ||
                        ($mtn->end_at >= $start && $mtn->end_at <= $end)) ||

                    ($mtn->start_at < $start && $mtn->end_at > $end)
                ) {
                    $conflictMaintenance = true;
                    break;
                }
            }

            // Add to available if no conflict
            if (!$conflictTasks && !$conflictMaintenance) {
                $available[] = $itemVessel;
            }
        }

        return $available;
    }

    /**
     * Suggest the earliest availability for each vessel after now.
     *
     */
    public static function getSuggestions($vessels, $start, $end)
    {
        $suggestions = [];

        foreach ($vessels as $itemVessel) {
            // get last conflicting end time at or after now — we will scan upcoming schedule
            // collect all task maintenances

            $now = Carbon::now();

            // collect all tasks and maintenances
            $busy = array();

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

            $busy = array_values($busy);

            // find first slot where a [start,end] interval fits
            $tryStart = $start->copy();

            // just set now if start is in the past
            if ($tryStart->lt($now)) $tryStart = $now->copy();

            $found = null;

            // iterate busy intervals and try to put inside of them
            foreach ($busy as $interval) {
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

            $suggestions[] = ['vessel_id' => $itemVessel->id, 'vessel_name' => $itemVessel->name, 'available_from' => $found->toIso8601String()];
        }

        usort($suggestions, function ($a, $b) {
            return strcmp($a['available_from'], $b['available_from']);
        });

        return $suggestions;
    }
}
