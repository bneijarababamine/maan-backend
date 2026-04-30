<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json(['status' => true, 'data' => $settings]);
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'key'   => 'required|string|max:100',
            'value' => 'nullable|string',
        ]);

        Setting::updateOrCreate(
            ['key'   => $request->key],
            ['value' => $request->value]
        );

        return response()->json(['status' => true, 'data' => null]);
    }
}
