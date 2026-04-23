<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    // Couleurs Google Calendar par niveau
    // https://developers.google.com/calendar/api/v3/reference/colors/get
    private const LEVEL_COLOR = [
        'bronze' => '6',  // Tangerine (orange)
        'silver' => '8',  // Graphite (gris)
        'gold'   => '5',  // Banana (jaune)
    ];

    private ?Calendar $calendar = null;

    private function client(): Calendar
    {
        if ($this->calendar) {
            return $this->calendar;
        }

        $client = new Client();
        $client->setApplicationName('WinBoard');
        $client->setScopes([Calendar::CALENDAR_EVENTS]);
        $client->setAuthConfig(config('services.google.service_account_json'));

        $this->calendar = new Calendar($client);
        return $this->calendar;
    }

    public function createEvent(
        string $title,
        string $description,
        \DateTimeInterface $start,
        int $durationMinutes = 60,
        string $level = 'bronze'
    ): ?string {
        $calendarId = config('services.google.calendar_id');

        if (!$calendarId || !file_exists(config('services.google.service_account_json'))) {
            return null;
        }

        try {
            $parisTz    = new \DateTimeZone('Europe/Paris');
            $startParis = (clone \DateTime::createFromInterface($start))->setTimezone($parisTz);
            $endParis   = (clone $startParis)->modify("+{$durationMinutes} minutes");

            Log::debug('GoogleCalendar createEvent', [
                'start' => $startParis->format('c'),
                'end'   => $endParis->format('c'),
            ]);

            $event = new Event([
                'summary'     => $title,
                'description' => $description,
                'colorId'     => self::LEVEL_COLOR[$level] ?? '7',
                'start'       => new EventDateTime(['dateTime' => $startParis->format('c'), 'timeZone' => 'Europe/Paris']),
                'end'         => new EventDateTime(['dateTime' => $endParis->format('c'),   'timeZone' => 'Europe/Paris']),
            ]);

            $created = $this->client()->events->insert($calendarId, $event);
            return $created->getId();
        } catch (\Exception $e) {
            Log::error('GoogleCalendar: ' . $e->getMessage());
            return null;
        }
    }
}
