<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the vote settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return view('vote::admin.settings', [
            'topPlayersCount' => setting('vote.top-players-count', 10),
        ]);
    }

    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function save(Request $request)
    {
        Setting::updateSettings('vote.top-players-count', $this->validate($request, [
            'top-players-count' => ['numeric', 'min:5', 'max:250'],
        ])['top-players-count']);
        Setting::updateSettings('vote.display-rewards', $request->has('display-rewards'));

        return redirect()->route('vote.admin.settings')->with('success', trans('admin.settings.status.updated'));
    }
}
