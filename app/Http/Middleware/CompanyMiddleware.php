<?php

namespace App\Http\Middleware;

use Closure;

class CompanyMiddleware
{
    public function handle($request, Closure $next): mixed
    {
        if ($request->user()) {
            $request->merge(['company_id' => $request->user()->company_id]);
            $this->userDateFormat($request);
        }

        return $next($request);
    }

    private function userDateFormat($request): void
    {
        $settings = $request->user()->setting;
        if ($settings) {
            $settingsDecoded = json_decode($settings, true);
            if ($this->date_format_exist($settingsDecoded)) {
                $request->merge(['user_date_format' => $settingsDecoded['settings']['date_format']]);
                $this->formatPhp($request, $settingsDecoded['settings']['date_format']);
            }
        } else {
            $request->merge(['user_date_format' => 'YYYY-MM-DD']);
            $this->formatPhp($request, 'Y-m-d');
        }
    }

    private function date_format_exist($settingsDecoded): bool
    {
        return $settingsDecoded['settings'] &&
            array_key_exists('date_format', $settingsDecoded['settings']) &&
            $settingsDecoded['settings']['date_format'];
    }

    private function formatPhp($request, $date_format): void
    {
        $new = str_replace('MM', 'm', str_replace('DD', 'd', str_replace('YYYY', 'Y', $date_format)));
        $request->merge(['user_date_format_php' => $new]);
    }
}
