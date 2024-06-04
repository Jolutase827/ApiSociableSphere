<?php

namespace App\Services;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Payout;
use PayPal\Api\PayoutSenderBatchHeader;
use PayPal\Api\PayoutItem;

class PayPalService
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                config('services.paypal.client_id'),
                config('services.paypal.client_secret')
            )
        );

        $this->apiContext->setConfig([
            'mode' => config('services.paypal.mode'),
        ]);
    }

    public function createPayout($amount, $recipientEmail)
    {
        $payouts = new Payout();

        $senderBatchHeader = new PayoutSenderBatchHeader();
        $senderBatchHeader->setSenderBatchId(uniqid())
            ->setEmailSubject("You have a payout!");

        $senderItem = new PayoutItem();
        $senderItem->setRecipientType('Email')
            ->setReceiver($recipientEmail)
            ->setAmount([
                'value' => $amount,
                'currency' => 'EUR'
            ])
            ->setNote("Thank you for using our service!");

        $payouts->setSenderBatchHeader($senderBatchHeader)
            ->addItem($senderItem);

        return $payouts->create([], $this->apiContext);
    }
}
