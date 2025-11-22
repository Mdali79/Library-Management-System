<?php

namespace App\Http\Controllers;

use App\Models\settings;
use App\Http\Requests\StoresettingsRequest;
use App\Http\Requests\UpdatesettingsRequest;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('settings',['data' => settings::latest()->first()]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatesettingsRequest  $request
     * @param  \App\Models\settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatesettingsRequest $request)
    {
        $setting = settings::latest()->first();
        
        if (!$setting) {
            $setting = new settings();
        }

        $setting->return_days = $request->return_days ?? 14;
        $setting->fine_per_day = $request->fine_per_day ?? 0;
        $setting->fine_grace_period_days = $request->fine_grace_period_days ?? 14;
        $setting->max_borrowing_limit_student = $request->max_borrowing_limit_student ?? 5;
        $setting->max_borrowing_limit_teacher = $request->max_borrowing_limit_teacher ?? 10;
        $setting->max_borrowing_limit_librarian = $request->max_borrowing_limit_librarian ?? 15;
        $setting->save();
        
        return redirect()->route('settings')->with('success', 'Settings updated successfully');
    }
}
