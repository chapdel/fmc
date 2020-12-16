<?php

namespace Spatie\Mailcoach\Http\App\Controllers\Automations;

use Spatie\Mailcoach\Http\App\Requests\AutomationRequest;
use Spatie\Mailcoach\Domain\Automation\Models\Automation;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class AutomationSettingsController
{
    use UsesMailcoachModels;

    public function edit(Automation $automation)
    {
        $triggerOptions = collect(config('mailcoach.automation.triggers'))
            ->map(function (string $trigger) {
                return $trigger::getName();
            });

        $emailLists = $this->getEmailListClass()::all();

        return view('mailcoach::app.automations.settings', [
            'automation' => $automation,
            'triggerOptions' => $triggerOptions,
            'emailLists' => $emailLists,
            'segmentsData' => $emailLists->map(function (EmailList $emailList) {
                return [
                    'id' => $emailList->id,
                    'name' => $emailList->name,
                    'segments' => $emailList->segments->map->only('id', 'name'),
                    'createSegmentUrl' => route('mailcoach.emailLists.segments', $emailList),
                ];
            }),
        ]);
    }

    public function update(
        Automation $automation,
        AutomationRequest $request
    ) {
        $automation->update([
            'name' => $request->get('name'),
            'email_list_id' => $request->email_list_id,
            'segment_class' => $request->getSegmentClass(),
            'segment_id' => $request->segment_id,
        ]);

        $automation->trigger($request->trigger());

        $automation->update(['segment_description' => $automation->getSegment()->description()]);

        flash()->success(__('Automation :automation was updated.', ['automation' => $automation->name]));

        return redirect()->route('mailcoach.automations.settings', $automation);
    }
}
