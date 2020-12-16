<?php

namespace Spatie\Mailcoach\Http\Api\Controllers\EmailLists;

use Spatie\Mailcoach\Domain\Campaign\Actions\EmailLists\UpdateEmailListAction;
use Spatie\Mailcoach\Http\Api\Controllers\Concerns\RespondsToApiRequests;
use Spatie\Mailcoach\Http\Api\Requests\EmailListRequest;
use Spatie\Mailcoach\Http\Api\Resources\EmailListResource;
use Spatie\Mailcoach\Http\App\Queries\EmailListQuery;
use Spatie\Mailcoach\Domain\Campaign\Models\EmailList;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class EmailListsController
{
    use RespondsToApiRequests, UsesMailcoachModels;

    public function index(EmailListQuery $emailLists)
    {
        return EmailListResource::collection($emailLists->paginate());
    }

    public function show(EmailList $emailList)
    {
        return new EmailListResource($emailList);
    }

    public function store(EmailListRequest $request, UpdateEmailListAction $updateEmailListAction)
    {
        $emailListClass = $this->getEmailListClass();

        $emailList = new $emailListClass;

        $emailList = $updateEmailListAction->execute($emailList, $request);

        return new EmailListResource($emailList);
    }

    public function update(EmailListRequest $request, EmailList $emailList, UpdateEmailListAction $updateEmailListAction)
    {
        $emailList = $updateEmailListAction->execute($emailList, $request);

        return new EmailListResource($emailList);
    }

    public function destroy(EmailList $emailList)
    {
        $emailList->delete();

        return $this->respondOk();
    }
}
