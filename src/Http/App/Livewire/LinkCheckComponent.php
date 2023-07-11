<?php

namespace Spatie\Mailcoach\Http\App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Spatie\Mailcoach\Domain\Shared\Actions\StripUtmTagsFromUrlAction;
use Throwable;

class LinkCheckComponent extends Component
{
    public string $url;

    public ?bool $status = null;

    public ?string $error = null;

    public bool $check = false;

    public string $strippedUrl;

    public function mount()
    {
        $this->strippedUrl = app(StripUtmTagsFromUrlAction::class)->execute($this->url);

        [$this->status, $this->error] = cache()->remember("link-check-{$this->strippedUrl}", now()->addHour(), function (): array {
            try {
                $response = Http::timeout(10)->get($this->strippedUrl);

                return [$response->successful(), $response->reason()];
            } catch (Throwable $e) {
                return [false, $e->getMessage()];
            }
        });

        if ($this->status === false) {
            cache()->forget("link-check-{$this->strippedUrl}");
        }
    }

    public function placeholder(): string
    {
        $url = app(StripUtmTagsFromUrlAction::class)->execute($this->url);

        return <<<"html"
        <span class="flex items-center">
            <span class="inline-flex w-4 mr-1">
                <i class="fas fa-spin fa-sync text-sm text-gray-400"></i>
            </span>
            <a target="_blank" class="link break-words" href="$url">$url</a>
        </span>
        html;
    }

    public function render(): string
    {
        return <<<'blade'
            <span class="flex items-center">
                <span class="inline-flex w-4 mr-1">
                    <x-mailcoach::health-label title="{{ $error }}" class="-ml-2" reverse warning :test="$status" />
                </span>
                <a target="_blank" class="link break-words" href="{{ $strippedUrl }}">{{ $strippedUrl }}</a>
            </span>
        blade;
    }
}
