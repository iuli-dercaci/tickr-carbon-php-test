<?php
declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\SubscriptionType;
use App\Services\ScheduleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScheduleController extends AbstractController
{
    /**
     * @Route("/carbon-offset-schedule", name="schedule")
     */
    public function index(
        Request $request,
        ScheduleService $scheduleService
    ): Response {
        $subscription = $this->createForm(
            SubscriptionType::class,
            [],
            ['csrf_protection' => false]
        )
            ->submit($request->query->all())
            ->handleRequest($request);

        if (!($subscription->isSubmitted() && $subscription->isValid())) {
            return $this->json($this->formErrors($subscription), 400);
        }

        $result = $scheduleService->generateSubscriptionDates(
            $subscription->get('subscriptionStartDate')->getData(),
            (int)$subscription->get('scheduleInMonths')->getData()
        );

        return $this->json($result);
    }

    private function formErrors(FormInterface $subscription): array
    {
        return array_map(function (FormError $error) {
            return $error->getMessage();
        },
            iterator_to_array(
                $subscription->getErrors(true, true)
            )
        );
    }
}
