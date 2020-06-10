<?php

namespace Spatie\Mailcoach\Http\App\Controllers\EmailLists;

use Spatie\Mailcoach\Http\App\Queries\EmailListTagsQuery;
use Spatie\Mailcoach\Http\App\Requests\CreateTagRequest;
use Spatie\Mailcoach\Http\App\Requests\UpdateTagRequest;
use Spatie\Mailcoach\Models\EmailList;
use Spatie\Mailcoach\Models\Tag;

class TagsController
{
    public function index(EmailList $emailList)
    {
        $tagsQuery = new EmailListTagsQuery($emailList);

        return view('mailcoach::app.emailLists.tag.index', [
            'emailList' => $emailList,
            'tags' => $tagsQuery->paginate(),
            'totalTagsCount' => Tag::query()->emailList($emailList)->count(),
        ]);
    }

    public function store(CreateTagRequest $request, EmailList $emailList)
    {
        $tag = $emailList->tags()->create(['name' => $request->name]);

        flash()->success(__('Tag :tag was created', ['tag' => $tag->name]));

        return back();
    }

    public function edit(EmailList $emailList, Tag $tag)
    {
        return view('mailcoach::app.emailLists.tag.edit', [
            'emailList' => $emailList,
            'tag' => $tag,
        ]);
    }

    public function update(UpdateTagRequest $request, EmailList $emailList, Tag $tag)
    {
        $tag->update([
            'name' => $request->name,
        ]);

        flash()->success(__('Tag :tag was updated', ['tag' => $tag->name]));

        return redirect()->route('mailcoach.emailLists.tags', $emailList);
    }

    public function destroy(EmailList $emailList, Tag $tag)
    {
        $tag->delete();

        flash()->success(__('Tag :tag was deleted', ['tag' => $tag->name]));

        return back();
    }
}
