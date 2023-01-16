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

    public function check(): void
    {
        [$this->status, $this->error] = cache()->remember("link-check-{$this->url}", now()->addHour(), function (): array {
            try {
                $response = Http::timeout(10)->get($this->url);

                return [$response->successful(), $response->reason()];
            } catch (Throwable $e) {
                return [false, $e->getMessage()];
            }
        });

        if ($this->status === false) {
            cache()->forget("link-check-{$this->url}");
        }
    }

    public function render(): string
    {
        $this->url = app(StripUtmTagsFromUrlAction::class)->execute($this->url);

        return <<<'blade'
            <span class="flex items-center" wire:init="check">
                <span class="inline-flex w-4 mr-1">
                    @if (!is_null($status))
                        <x-mailcoach::health-label title="{{ $error }}" class="-ml-2" reverse warning :test="$status" />
                    @else
                        <i class="fas fa-spin fa-sync text-sm text-gray-400"></i>
                    @endif
                </span>
                <a target="_blank" class="link break-words" href="{{ $url }}">{{ $url }}</a>
            </span>
        blade;
    }
}
