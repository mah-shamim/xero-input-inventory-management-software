<?php

namespace App\Rules;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Contracts\Validation\Rule;

class Recaptcha implements Rule
{
    protected $error_codes;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $vars = [
            'secret' => google_recaptcha()['secret_key'],
            'response' => request()->input('recaptcha_v3'),
        ];
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $client = new Client();
        try {
            $response = $client->post($url, [
                'form_params' => [
                    'secret' => $vars['secret'],
                    'response' => $vars['response'],
                    'remoteip' => request()->ip(),
                ],
            ]);
        } catch (BadResponseException $exception) {
            return false;
        }
        $response = \GuzzleHttp\json_decode($response->getBody(), true);

        if (! $response['success']) {
            $this->error_codes = $response['error-codes'];
        }
        if ($response['success'] && ! $response['score'] >= (int) google_recaptcha()['score'] / 10) {
            $this->error_codes = $response['error-codes'];
        }

        return $response['success'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $msg = 'Recaptcha failed';
        if (! empty($this->error_codes)) {
            $msg = implode(', ', $this->error_codes);
        }

        return $msg;
    }
}
