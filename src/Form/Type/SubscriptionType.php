<?php
declare(strict_types=1);

namespace App\Form\Type;

use DateTime;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SubscriptionType extends AbstractType
{
    public const DATE_FORMAT = 'Y-m-d';
    public const MAX_PERIOD = 36;
    public const MIN_PERIOD = 0;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
                'scheduleInMonths',
                TextType::class,
                [
                    'constraints' => [
                        new Callback([$this, 'scheduleInMonthsValidate']),
                    ],
                ]
            )
            ->add(
                'subscriptionStartDate',
                TextType::class,
                [
                    'constraints' => [
                        new Callback([$this, 'subscriptionStartDateValidate']),
                    ],
                ],
            );
    }

    public function subscriptionStartDateValidate(?string $subscriptionStartDate, ExecutionContextInterface $context): void
    {
        $startDate = $subscriptionStartDate
            ? DateTime::createFromFormat(self::DATE_FORMAT, $subscriptionStartDate)
            : null;

        $today = (new DateTime('today'))->setTime(23, 59, 59);

        if (!($startDate instanceof DateTimeInterface)) {
            $context->buildViolation('[subscriptionStartDate] must be a date in [YYYY-mm-dd] format')
                ->atPath('subscriptionStartDate')
                ->addViolation();
        } elseif ($startDate > $today) {
            $context->buildViolation('[subscriptionStartDate] cannot be in the future')
                ->atPath('subscriptionStartDate')
                ->addViolation();
        }
    }

    public function scheduleInMonthsValidate(?string $monthsAmount, ExecutionContextInterface $context): void
    {
        if (!is_numeric($monthsAmount)) {
            $context->buildViolation('[scheduleInMonths] must be a number')
                ->atPath('scheduleInMonths')
                ->addViolation();
        } elseif ((int)$monthsAmount < self::MIN_PERIOD) {
            $context->buildViolation(
                sprintf('[scheduleInMonths] must be greater or equal than %d', self::MIN_PERIOD)
            )
                ->atPath('scheduleInMonths')
                ->addViolation();
        } elseif ((int)$monthsAmount > self::MAX_PERIOD) {
            $context->buildViolation(
                sprintf('[scheduleInMonths] must be less or equal than %d', self::MAX_PERIOD)
            )
                ->atPath('scheduleInMonths')
                ->addViolation();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}