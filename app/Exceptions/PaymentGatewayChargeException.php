<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentGatewayChargeException extends Exception
{
    /**
     * @var array
     */
    private $data;

    public function __construct($message = "", array $data)
    {
        $this->data = $data;
        parent::__construct($message);
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  Request  $request
     */
    public function render($request)
    {
        $data = $this->getData();
        Log::error('Card failed: ', $data);
        $template = 'partials.errors.charge_failed';
        $data = $data['error'];

        return view('errors.generic', compact('template', 'data'));
    }

    public function getData(): array
    {
        return $this->data;
    }

}
