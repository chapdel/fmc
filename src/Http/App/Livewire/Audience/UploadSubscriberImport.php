<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\User;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Mailcoach\Domain\Audience\Jobs\ImportSubscribersJob;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class UploadSubscriberImport extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;
    use UsesMailcoachModels;

    public EmailList $emailList;

    public string $replaceTags = 'append';

    public bool $subscribeUnsubscribed = false;

    public bool $unsubscribeMissing = false;

    public $file;

    public function upload()
    {
        $this->authorize('update', $this->emailList);

        /** @var \Spatie\Mailcoach\Domain\Audience\Models\SubscriberImport $subscriberImport */
        $subscriberImport = self::getSubscriberImportClass()::create([
            'email_list_id' => $this->emailList->id,
            'subscribe_unsubscribed' => $this->subscribeUnsubscribed,
            'unsubscribe_others' => $this->unsubscribeMissing,
            'replace_tags' => $this->replaceTags === 'replace',
        ]);

        /** @var \Livewire\TemporaryUploadedFile $file */
        $file = $this->file;
        $path = $file->store('subscriber-import');

        $subscriberImport->addMediaFromDisk($path, $file->disk)->toMediaCollection('importFile');

        $user = auth()->user();

        dispatch(new ImportSubscribersJob($subscriberImport, $user instanceof User ? $user : null));

        flash()->success(__('mailcoach - Your file has been uploaded. Follow the import status in the list below.'));

        return redirect(request()->header('Referer'));
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;
    }

    public function render()
    {
        return view('mailcoach::app.emailLists.subscribers.upload-import');
    }
}
