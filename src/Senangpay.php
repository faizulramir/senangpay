<?php

namespace Faizulramir\Senangpay;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;

class Senangpay
{
    protected $merchantId;
    protected $secretKey;
    protected $isSandbox;

    public function __construct()
    {
        $this->merchantId = config('senangpay.merchant_id');
        $this->secretKey = config('senangpay.secret_key');
        $this->isSandbox = config('senangpay.sandbox', false);

        if (empty($this->merchantId) || empty($this->secretKey)) {
            throw new \InvalidArgumentException('SenangPay merchant ID and secret key must be configured');
        }
    }

    public function createPayment($params)
    {
        $requiredParams = ['detail', 'amount', 'order_id', 'name', 'email', 'phone'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }

        // Get return URL from config
        $returnUrl = config('senangpay.return_url');
        if (empty($returnUrl)) {
            throw new \InvalidArgumentException('SenangPay return URL must be configured');
        }

        // Generate hash using HMAC SHA256 exactly like the sample code
        $hash = hash_hmac(
            'sha256',
            $this->secretKey .
            urldecode($params['detail']) .
            urldecode($params['amount']) .
            urldecode($params['order_id']),
            $this->secretKey
        );

        // Generate the form HTML
        $baseUrl = $this->isSandbox
            ? 'https://sandbox.senangpay.my/payment/'
            : 'https://app.senangpay.my/payment/';

        $html = '<form name="order" method="post" action="' . $baseUrl . $this->merchantId . '">';
        $html .= '<input type="hidden" name="detail" value="' . htmlspecialchars($params['detail']) . '">';
        $html .= '<input type="hidden" name="amount" value="' . htmlspecialchars($params['amount']) . '">';
        $html .= '<input type="hidden" name="order_id" value="' . htmlspecialchars($params['order_id']) . '">';
        $html .= '<input type="hidden" name="name" value="' . htmlspecialchars($params['name']) . '">';
        $html .= '<input type="hidden" name="email" value="' . htmlspecialchars($params['email']) . '">';
        $html .= '<input type="hidden" name="phone" value="' . htmlspecialchars($params['phone']) . '">';
        $html .= '<input type="hidden" name="hash" value="' . $hash . '">';
        $html .= '<input type="hidden" name="return_url" value="' . htmlspecialchars($returnUrl) . '">';
        $html .= '</form>';
        $html .= '<script>document.order.submit();</script>';

        return $html;
    }

    public function verifyPayment($params)
    {
        $requiredParams = ['status_id', 'order_id', 'msg', 'transaction_id', 'hash'];
        foreach ($requiredParams as $param) {
            if (!isset($params[$param])) {
                throw new \InvalidArgumentException("Missing required parameter: {$param}");
            }
        }

        // Generate hash using HMAC SHA256 exactly like the sample code
        $hash = hash_hmac(
            'sha256',
            $this->secretKey .
            urldecode($params['status_id']) .
            urldecode($params['order_id']) .
            urldecode($params['transaction_id']) .
            urldecode($params['msg']),
            $this->secretKey
        );

        return $hash === urldecode($params['hash']);
    }
}