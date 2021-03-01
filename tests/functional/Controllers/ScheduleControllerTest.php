<?php
declare(strict_types=1);

use App\Form\Type\SubscriptionType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    /**
     * @dataProvider validParamsData
     */
    public function testIndexWorksWithValidUrlParams(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);
        $response = $client->getResponse();

        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertTrue($response->isSuccessful());
    }

    /**
     * @dataProvider missingParamsData
     */
    public function testIndexFailsInvalidParameters(string $url, string $errorMsg): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(400);
        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertStringContainsString($errorMsg, $client->getResponse()->getContent());
    }

    public function missingParamsData(): array
    {
        $validDate = new DateTime('previous month');
        $futureDate = new DateTime('tomorrow');

        return [
            'no subscription start date' => [
                $this->assembleUrl(null, 1),
                '[subscriptionStartDate] must be a date in [YYYY-mm-dd] format',
            ],
            'no schedule in months' => [
                $this->assembleUrl($validDate->format('Y-m-d')),
                '[scheduleInMonths] must be a number',
            ],
            'invalid date' => [
                $this->assembleUrl($futureDate->format('Y-m-d'), 2),
                '[subscriptionStartDate] cannot be in the future',
            ],
            'negative months value' => [
                $this->assembleUrl($validDate->format('Y-m-d'), -2),
                '[scheduleInMonths] must be greater or equal than ' . SubscriptionType::MIN_PERIOD,
            ],
            'too large months value' => [
                $this->assembleUrl($validDate->format('Y-m-d'), 200),
                '[scheduleInMonths] must be less or equal than ' . SubscriptionType::MAX_PERIOD,
            ],
        ];
    }

    public function validParamsData(): array
    {
        $date = new DateTime('previous month');
        return [
            'valid params' => [$this->assembleUrl($date->format('Y-m-d'), 1)],
            'zero length' => [$this->assembleUrl($date->format('Y-m-d'), 0)],
        ];
    }

    private function assembleUrl(?string $date = null, ?int $length = null): string
    {
        $params = [];
        if (null !== $date) {
            $params['subscriptionStartDate'] = $date;
        }
        if (null !== $length) {
            $params['scheduleInMonths'] = $length;
        }

        return '/carbon-offset-schedule?' . http_build_query($params);
    }
}