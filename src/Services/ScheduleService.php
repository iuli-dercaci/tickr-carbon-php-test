<?php
declare(strict_types=1);

namespace App\Services;

use App\Form\Type\SubscriptionType;
use DateTime;
use DateTimeInterface;

class ScheduleService
{
    public function generateSubscriptionDates(string $startDate, int $months): array
    {
        $result = [];
        $date = DateTime::createFromFormat(SubscriptionType::DATE_FORMAT, $startDate);
        $day = (int)$date->format('d');

        for ($i = 0; $i < $months; $i++) {
            $date = $this->getNextMonth(
                $day,
                (int)$date->format('m'),
                (int)$date->format('Y')
            );
            $result[] = $date->format(SubscriptionType::DATE_FORMAT);
        }

        return $result;
    }

    public function getNextMonth(int $day, int $month, int $year): DateTimeInterface
    {
        $month = $month == 12 ? 1 : $month + 1;
        $year = $month == 1 ? $year + 1: $year;
        $day = $this->getValidDay($day, $month, $year);
        return (new DateTime())->setDate($year, $month, $day);
    }

    private function getValidDay(int $day, int $month, int $year): int
    {
        if (!checkdate($month, $day, $year)) {
            $date = (new DateTime())->setDate($year, $month, 1);
            $day = (int)$date->format('t');
        }
        return $day;
    }
}