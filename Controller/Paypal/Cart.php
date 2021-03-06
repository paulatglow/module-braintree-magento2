<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Controller\Paypal;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Webapi\Exception;

/**
 * Class GetNonce
 */
class Cart extends Action
{
    /**
     * @var \Magento\Braintree\Model\Paypal\CreditApi
     */
    private $creditApi;

    /**
     * Cart constructor.
     * @param Context $context
     * @param \Magento\Braintree\Model\Paypal\CreditApi $creditApi
     */
    public function __construct(
        Context $context,
        \Magento\Braintree\Model\Paypal\CreditApi $creditApi
    ) {
        parent::__construct($context);
        $this->creditApi = $creditApi;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $amount = number_format($this->getRequest()->getParam("amount", 0), 2, '.', '');
        
        if (!$amount || $amount <= 0) {
            return $this->processBadRequest($response);
        }

        try {
            $results = $this->creditApi->getPriceOptions($amount);
            $options = [];
            foreach ($results as $priceOption) {
                $options[] = [
                    'term' => $priceOption['term'],
                    'monthlyPayment' => $priceOption['monthly_payment'],
                    'apr' => $priceOption['instalment_rate'],
                    'cost' => $priceOption['cost_of_purchase'],
                    'costIncInterest' => $priceOption['total_inc_interest']
                ];
            }

            $response->setData($options);
        } catch (\Exception $e) {
            return $this->processBadRequest($response);
        }

        return $response;
    }

    /**
     * Return response for bad request
     * @param ResultInterface $response
     * @return ResultInterface
     */
    private function processBadRequest(ResultInterface $response)
    {
        $response->setHttpResponseCode(Exception::HTTP_BAD_REQUEST);
        $response->setData(['message' => __('No Credit Options available')]);

        return $response;
    }
}
