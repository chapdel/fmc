<?php

namespace Spatie\Mailcoach\Http\App\Livewire\Audience;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;
use Spatie\Mailcoach\Http\App\Livewire\LivewireFlash;
use Spatie\Mailcoach\MainNavigation;

class Website extends Component
{
    use UsesMailcoachModels;
    use LivewireFlash;
    use WithFileUploads;

    /** @var \Illuminate\Http\UploadedFile */
    public $image;

    public EmailList $emailList;

    protected function rules(): array
    {
        $rules = [
            'emailList.has_website' => ['boolean'],
            'emailList.show_subscription_form_on_website' => ['boolean'],
            'emailList.website_slug' => ['nullable'],
            'emailList.website_title' => ['nullable'],
            'emailList.website_intro' => ['nullable'],
            'emailList.website_primary_color' => ['nullable'],
        ];

        if ($this->image) {
            $rules['image'] = ['', 'image', 'max:2048'];
        }

        return $rules;
    }

    public function mount(EmailList $emailList)
    {
        $this->emailList = $emailList;

        app(MainNavigation::class)->activeSection()
            ->add($this->emailList->name, route('mailcoach.emailLists.website', $this->emailList));
    }

    public function save()
    {
        $this->validate();

        if ($this->image) {
            $path = $this->handleUpload();

            if (! $path) {
                $this->flashError('Upload failed. Please try again');

                return;
            }

            $this
                ->emailList
                ->addMedia($path)
                ->toMediaLibrary('header', config('mailcoach.website_disk'));
        }

        $this->emailList->save();

        $this->flash(__('mailcoach - Website settings for list :emailList were updated', ['emailList' => $this->emailList->name]));
    }

    public function render(): View
    {
        return view('mailcoach::app.emailLists.settings.website')
            ->layout('mailcoach::app.emailLists.layouts.emailList', [
                'title' => __('mailcoach - Website'),
                'emailList' => $this->emailList,
            ]);
    }

    protected function handleUpload(): ?string
    {
        $diskName = config('mailcoach.tmp_disk');

        $relativePath = $this->image->store('uploads', [
            'disk' => $diskName,
        ]);

        if (! $relativePath) {
            return $relativePath;
        }

        return Storage::disk($diskName)->path($relativePath);
    }
}
