# Laravel SenangPay Package

A Laravel package for integrating SenangPay payment gateway into your Laravel applications.

## Features

- Easy integration with SenangPay payment gateway
- Support for both sandbox and production environments
- Form-based payment submission
- Payment response handling
- Hash verification for secure transactions
- Configurable through environment variables

## Requirements

- PHP >= 8.1
- Laravel >= 10.0
- Composer

## Installation

1. Install the package via Composer:

```bash
composer require faizulramir/senangpay
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="Faizulramir\Senangpay\SenangpayServiceProvider"
```

3. Add the following environment variables to your `.env` file:

```
SENANGPAY_MERCHANT_ID=your_merchant_id
SENANGPAY_SECRET_KEY=your_secret_key
SENANGPAY_IS_SANDBOX=true
```

## Configuration

The package configuration file (`config/senangpay.php`) contains the following options:

```php
return [
    'merchant_id' => env('SENANGPAY_MERCHANT_ID'),
    'secret_key' => env('SENANGPAY_SECRET_KEY'),
    'is_sandbox' => env('SENANGPAY_IS_SANDBOX', true),
];
```

## Usage

### Basic Payment Form

```php
use Faizulramir\Senangpay\Senangpay;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        $senangpay = new Senangpay();
        return view('payment.form', compact('senangpay'));
    }
}
```

In your Blade view:

```php
<form action="{{ $senangpay->getPaymentUrl() }}" method="POST">
    @csrf
    <input type="hidden" name="detail" value="Order #123">
    <input type="hidden" name="amount" value="100.00">
    <input type="hidden" name="order_id" value="ORDER123">
    <input type="hidden" name="name" value="John Doe">
    <input type="hidden" name="email" value="john@example.com">
    <input type="hidden" name="phone" value="60123456789">
    <input type="hidden" name="hash" value="{{ $senangpay->generateHash([
        'detail' => 'Order #123',
        'amount' => '100.00',
        'order_id' => 'ORDER123'
    ]) }}">
    <button type="submit">Pay Now</button>
</form>
```

### Handling Payment Response

```php
public function handlePaymentResponse(Request $request)
{
    $senangpay = new Senangpay();
    
    if ($senangpay->verifyHash($request->all())) {
        // Payment successful
        return view('payment.success');
    }
    
    // Payment failed
    return view('payment.failed');
}
```

## Available Methods

### `getPaymentUrl()`
Returns the SenangPay payment URL based on the environment (sandbox/production).

### `generateHash(array $data)`
Generates a hash for payment verification using the following parameters:
- detail
- amount
- order_id

### `verifyHash(array $data)`
Verifies the payment response hash to ensure the transaction is legitimate.

## Important Notes

- Always validate payment responses using the `verifyHash()` method
- Keep your secret key secure and never expose it in client-side code
- Test thoroughly in sandbox mode before going live
- Make sure to handle both success and failure scenarios in your application

## Security

- The package uses SHA256 hashing for secure transaction verification
- All sensitive data is handled server-side
- Environment variables are used for configuration
- Hash verification is implemented to prevent tampering

## Support

If you encounter any issues or have questions, please open an issue in the GitHub repository.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md). 