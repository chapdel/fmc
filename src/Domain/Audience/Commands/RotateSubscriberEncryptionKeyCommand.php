<?php

namespace Spatie\Mailcoach\Domain\Audience\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Mailcoach\Domain\Shared\Traits\UsesMailcoachModels;

class RotateSubscriberEncryptionKeyCommand extends Command
{
    use UsesMailcoachModels;

    public $signature = 'mailcoach:rotate-encryption-key {oldKey} {newKey} {oldCipher?} {newCipher?}';

    public $description = 'Decrypt user personal information with the old key and encrypt it with the new key.';

    public function handle()
    {
        $this->comment('Saving subscribers using new encryption key...');

        $oldCipher = $this->argument('oldCipher') ?? config('mailcoach.encryption.cipher');
        $newCipher = $this->argument('newCipher') ?? config('mailcoach.encryption.cipher');

        $oldEncrypter = new class {
            public function decryptString(string $string): string
            {
                return $string;
            }
        };
        if ($this->argument('oldKey')) {
            $oldEncrypter = new Encrypter($this->parseKey($this->argument('oldKey')), $oldCipher);
        }

        $newEncrypter = new class {
            public function encryptString(string $string): string
            {
                return $string;
            }
        };
        if ($this->argument('newKey')) {
            $newEncrypter = new Encrypter($this->parseKey($this->argument('newKey')), $newCipher);
        }

        $this->getOutput()->progressStart(self::getSubscriberClass()::count());
        DB::table(self::getSubscriberTableName())
            ->lazyById()
            ->each(function ($subscriber) use ($oldEncrypter, $newEncrypter) {
                $email = $oldEncrypter->decryptString($subscriber->email);

                $firstName = $subscriber->first_name ? $oldEncrypter->decryptString($subscriber->first_name) : $subscriber->first_name;
                $lastName = $subscriber->last_name ? $oldEncrypter->decryptString($subscriber->last_name) : $subscriber->last_name;

                $extraAttributes = null;
                if ($subscriber->extra_attributes) {
                    $extraAttributes = json_decode($oldEncrypter->decryptString($subscriber->extra_attributes), true);
                }

                DB::table(self::getSubscriberTableName())->where('id', $subscriber->id)->update([
                    'email' => $newEncrypter->encryptString($email),
                    'first_name' => $firstName
                        ? $newEncrypter->encryptString($firstName)
                        : $firstName,
                    'last_name' => $lastName
                        ? $newEncrypter->encryptString($lastName)
                        : $lastName,
                    'extra_attributes' => $extraAttributes
                        ? $newEncrypter->encryptString(json_encode($extraAttributes))
                        : null,
                ]);

                $this->getOutput()->progressAdvance();
            });

        $this->getOutput()->progressFinish();
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
