<?php

namespace App\Factories;

use App\Interfaces\IPaymentCalculator;
use App\Decorators\PercentageDiscountDecorator;
use App\Decorators\FixedDiscountDecorator;
use App\Decorators\LateFeeDecorator;
use App\Decorators\ProcessingFeeDecorator;
use App\Decorators\TaxDecorator;
use App\Decorators\BonusDecorator;

class PaymentDecoratorFactory
{
    public static function applyDiscount(IPaymentCalculator $payment, array $discountConfig): IPaymentCalculator
    {
        $decoratedPayment = $payment;

        if (isset($discountConfig['percentage'])) {
            $decoratedPayment = new PercentageDiscountDecorator(
                $decoratedPayment,
                $discountConfig['percentage'],
                $discountConfig['description'] ?? 'Percentage discount'
            );
        }

        if (isset($discountConfig['fixed_amount'])) {
            $decoratedPayment = new FixedDiscountDecorator(
                $decoratedPayment,
                $discountConfig['fixed_amount'],
                $discountConfig['description'] ?? 'Fixed discount'
            );
        }

        return $decoratedPayment;
    }

    public static function applyFees(IPaymentCalculator $payment, array $feeConfig): IPaymentCalculator
    {
        $decoratedPayment = $payment;

        if (isset($feeConfig['late_fee'])) {
            $decoratedPayment = new LateFeeDecorator(
                $decoratedPayment,
                $feeConfig['late_fee'],
                'Late payment penalty'
            );
        }

        if (isset($feeConfig['processing_fee'])) {
            $decoratedPayment = new ProcessingFeeDecorator(
                $decoratedPayment,
                $feeConfig['processing_fee'],
                'Processing fee'
            );
        }

        if (isset($feeConfig['tax_rate'])) {
            $decoratedPayment = new TaxDecorator(
                $decoratedPayment,
                $feeConfig['tax_rate'],
                'VAT'
            );
        }

        if (isset($feeConfig['bonus'])) {
            $decoratedPayment = new BonusDecorator(
                $decoratedPayment,
                $feeConfig['bonus'],
                'Performance bonus'
            );
        }

        return $decoratedPayment;
    }

    public static function createDecoratedPayment(IPaymentCalculator $payment, array $config): IPaymentCalculator
    {
        $decoratedPayment = $payment;

        // Apply discounts first
        if (isset($config['discounts'])) {
            $decoratedPayment = self::applyDiscount($decoratedPayment, $config['discounts']);
        }

        // Then apply fees
        if (isset($config['fees'])) {
            $decoratedPayment = self::applyFees($decoratedPayment, $config['fees']);
        }

        return $decoratedPayment;
    }
}