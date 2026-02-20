<?php

namespace App\Services;

class GstCalculator
{
    /**
     * Calculate GST for a single invoice item.
     *
     * @param  float  $itemPrice  Unit price
     * @param  int  $quantity  Quantity
     * @param  float  $gstRate  GST rate (e.g. 0, 5, 12, 18)
     * @param  string|null  $sellerStateCode  Seller's state code (2 digits)
     * @param  string|null  $clientStateCode  Client's state code (2 digits)
     * @return array{cgst: float, sgst: float, igst: float, item_total_with_tax: float}
     */
    public static function calculate(
        float $itemPrice,
        int $quantity,
        float $gstRate,
        ?string $sellerStateCode,
        ?string $clientStateCode
    ): array {
        $taxableValue = round($itemPrice * $quantity, 2);
        $totalGst = round($taxableValue * ($gstRate / 100), 2);
        $itemTotalWithTax = round($taxableValue + $totalGst, 2);

        $sellerState = trim((string) ($sellerStateCode ?? ''));
        $clientState = trim((string) ($clientStateCode ?? ''));

        $isSameState = $sellerState !== '' && $clientState !== '' && $sellerState === $clientState;

        if ($isSameState) {
            $cgst = round($totalGst / 2, 2);
            $sgst = round($totalGst / 2, 2);
            $igst = 0.0;
        } else {
            $cgst = 0.0;
            $sgst = 0.0;
            $igst = $totalGst;
        }

        return [
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'item_total_with_tax' => $itemTotalWithTax,
        ];
    }
}
