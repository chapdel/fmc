<?php

namespace Spatie\Mailcoach\Domain\Shared\Jobs\Export;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExportSubscribersJob extends ExportJob
{
    /**
     * @param string $path
     * @param array<int> $selectedEmailLists
     */
    public function __construct(protected string $path, protected array $selectedEmailLists)
    {
    }

    public function name(): string
    {
        return 'Subscribers';
    }

    public function execute(): void
    {
        $subscribersCount = 0;

        DB::table(self::getSubscriberTableName())
            ->select(self::getSubscriberTableName(). '.*', DB::raw(self::getEmailListTableName() . '.uuid as email_list_uuid'))
            ->join(self::getEmailListTableName(), self::getEmailListTableName() . '.id', self::getSubscriberTableName().'.email_list_id')
            ->orderBy('id')
            ->whereIn('email_list_id', $this->selectedEmailLists)
            ->chunk(10_000, function (Collection $subscribers, $index) use (&$subscribersCount) {
                $subscribersCount += $subscribers->count();

                if (config('mailcoach.encryption.enabled')) {
                    $encrypter = self::getSubscriberClass()::getEncryptedRow();

                    $subscribers = $subscribers->map(function ($subscriber) use ($encrypter) {
                        $decrypted = $encrypter->decryptRow([
                            'email' => $subscriber->email,
                            'first_name' => $subscriber->first_name,
                            'last_name' => $subscriber->last_name,
                        ]);
                        $subscriber->email = $decrypted['email'];
                        $subscriber->first_name = $decrypted['first_name'];
                        $subscriber->last_name = $decrypted['last_name'];

                        return $subscriber;
                    });
                }

                $this->writeFile("subscribers-{$index}.csv", $subscribers);
            });

        $this->addMeta('subscribers_count', $subscribersCount);
    }

    private function parseKey(string $key): string
    {
        $key = trim($key);

        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
