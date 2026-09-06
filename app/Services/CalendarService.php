<?php

namespace App\Services;

use App\Models\User;
use App\Services\Concerns\RefreshesGoogleToken;
use Carbon\Carbon;
use Google\Service\Calendar;
use Google\Service\Calendar\FreeBusyRequest;
use Google\Service\Calendar\FreeBusyRequestItem;

class CalendarService
{
    use RefreshesGoogleToken;

    protected Calendar $service;

    public function __construct(User $user)
    {
        $this->service = new Calendar($this->buildAuthenticatedClient($user));
    }

    /**
     * Tìm khung giờ trống gần nhất trong giờ hành chính (9h-17h), tránh trùng lịch bận.
     */
    public function findNextAvailableSlot(int $durationMinutes = 30, int $searchDays = 5): ?array
    {
        $timeMin = now();
        $timeMax = now()->addDays($searchDays);

        $freeBusyRequest = new FreeBusyRequest([
            'timeMin' => $timeMin->toRfc3339String(),
            'timeMax' => $timeMax->toRfc3339String(),
            'items' => [new FreeBusyRequestItem(['id' => 'primary'])],
        ]);

        $response = $this->service->freebusy->query($freeBusyRequest);
        $busyPeriods = $response->getCalendars()['primary']->getBusy();

        $busyRanges = collect($busyPeriods)->map(fn($p) => [
            'start' => Carbon::parse($p->getStart()),
            'end' => Carbon::parse($p->getEnd()),
        ]);

        $cursor = $timeMin->copy()->addHour()->minute(0)->second(0);

        while ($cursor->lt($timeMax)) {
            if ($cursor->hour < 9 || $cursor->hour >= 17) {
                $cursor->addDay()->hour(9)->minute(0);
                continue;
            }

            $slotEnd = $cursor->copy()->addMinutes($durationMinutes);

            $overlaps = $busyRanges->contains(
                fn($range) => $cursor->lt($range['end']) && $slotEnd->gt($range['start'])
            );

            if (!$overlaps) {
                return ['start' => $cursor->copy(), 'end' => $slotEnd->copy()];
            }

            $cursor->addMinutes(30);
        }

        return null;
    }
}