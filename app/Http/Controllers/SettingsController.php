<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsRequest;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Opcodes\LogViewer\Logs\Log;

class SettingsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): JsonResponse
    {
        return response()->json(
            [
                'type' => 'success',
                'settings' => $this->getSettings(),
                'company_detail' => Company::whereId(request()->user()->company_id)->first()
            ]);

    }

    public function store(SettingsRequest $request): JsonResponse
    {
        if ($request->input('company_detail') == true) {
            $this->updateCompany();
        } else {
            $this->updateSettings();
        }

        return $this->index();
    }

    private function updateCompany(): void
    {
        Company::where('id', request()->user()->company_id)
            ->update(request()
                ->except('company_id', 'company_detail', 'user_date_format', 'user_date_format_php'));
    }

    private function updateSettings(): void
    {
        $userSettings = $this->getSettings();
        $userSettings->update(['settings' => request()->all()]);
    }

    function getSettings(): mixed
    {
        return Setting::where('company_id', request()->user()->company_id)->first();
    }
}
