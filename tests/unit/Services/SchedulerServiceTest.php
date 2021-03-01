<?php
declare(strict_types=1);

use App\Form\Type\SubscriptionType;
use App\Services\ScheduleService;
use PHPUnit\Framework\TestCase;

class SchedulerServiceTest extends TestCase
{
    private ScheduleService $service;

    public function setUp(): void
    {
        $this->service = new ScheduleService();
    }

    /**
     * @dataProvider nextDateData
     */
    public function testCanGetNextDate(
        int $day,
        int $month,
        int $year,
        DateTimeInterface $expectedDate
    ): void {
        $actual = $this->service->getNextMonth($day, $month, $year)->format(SubscriptionType::DATE_FORMAT);
        $expected = $expectedDate->format(SubscriptionType::DATE_FORMAT);
        self::assertEquals($expected, $actual);
    }

    /**
     * @dataProvider datesCollectionData
     */
    public function testCanGetDatesCollection(
        string $startDate,
        int $length,
        array $expected
    ): void {
        $actual = $this->service->generateSubscriptionDates($startDate, $length);
        self::assertSame($expected, $actual);
    }

    public function datesCollectionData()
    {
        return [
            '2 months' => [
                '2020-01-01', 2, ['2020-02-01', '2020-03-01'],
            ],
            '3 months last day' => [
                '2020-01-31', 3, ['2020-02-29', '2020-03-31', '2020-04-30'],
            ],
        ];
    }

    public function nextDateData(): array
    {
        return [
            'regular case' => [
                1, 1, 2020, DateTime::createFromFormat('Y-m-d', '2020-02-01'),
            ],
            'last month' => [
                1, 12, 2020, DateTime::createFromFormat('Y-m-d', '2021-01-01'),
            ],
            '31st of a month' => [
                31, 10, 2020, DateTime::createFromFormat('Y-m-d', '2020-11-30'),
            ],
            '31st of a month and end of a year' => [
                31, 12, 2020, DateTime::createFromFormat('Y-m-d', '2021-01-31'),
            ],
            '31st of a month and February' => [
                31, 01, 2021, DateTime::createFromFormat('Y-m-d', '2021-02-28'),
            ],
            '31st of a month and February leap year' => [
                31, 01, 2020, DateTime::createFromFormat('Y-m-d', '2020-02-29'),
            ],
        ];
    }
}