<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\Mailcoach\Domain\Audience\Events\TagRemovedEvent;
use Spatie\Mailcoach\Domain\Audience\Models\EmailList;
use Spatie\Mailcoach\Domain\Audience\Models\Tag;
use Spatie\Mailcoach\Domain\Campaign\Enums\TagType;
use Spatie\Mailcoach\Http\App\Queries\EmailListTagsQuery;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\CreateTagRequest;
use Spatie\Mailcoach\Http\App\Requests\EmailLists\UpdateTagRequest;

class TagsController
{
    use AuthorizesRequests;

    public function edit(EmailList $emailList, Tag $tag)
    {
        $this->authorize('update', $emailList);

        return view('mailcoach::app.emailLists.tags.edit', [
            'emailList' => $emailList,
            'tag' => $tag,
        ]);
    }

    public function update(UpdateTagRequest $request, EmailList $emailList, Tag $tag)
    {
        $this->authorize('update', $emailList);

        $tag->update([
            'name' => $request->name,
        ]);

        flash()->success(__('mailcoach - Tag :tag was updated', ['tag' => $tag->name]));

        return redirect()->route('mailcoach.emailLists.tags', $emailList);
    }
}
