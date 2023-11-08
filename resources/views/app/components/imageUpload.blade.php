@php($wireModelAttribute = collect($attributes)->first(fn (string $value, string $attribute) => str_starts_with($attribute, 'wire:model')))
<div x-cloak x-data="{
    imageUrl: @entangle($wireModelAttribute),
    error: '',
    loading: false,
    fileChosen(event) {
        this.loading = true;
        this.error = '';
        let file = event.target.files[0];

        if (file.size > 1024 * 1024 * 2) {
            this.loading = false;
            this.error = 'File cannot be larger than 2MB.';
            return;
        }

        if (file.type.split('/')[0] !== 'image') {
            this.loading = false;
            this.error = 'File must be an image.';
            return;
        }

        const data = new FormData();
        data.append('file', file);

        fetch('{{ action(\Spatie\Mailcoach\Http\Api\Controllers\UploadsController::class) }}', {
            method: 'POST',
            body: data,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-Token': '{{ csrf_token() }}',
            },
        }).then(response => response.json())
          .then(({ success, file }) => {
              this.loading = false;
              if (! success) {
                  this.error = 'Something went wrong';
                  return;
              }

              this.imageUrl = file.url;
          });
    },
}">
    <div class="flex gap-6" x-show="!imageUrl">
        <div>
            <input accept="image/png,image/jpg,image/jpeg,image/gif" type="file" x-on:change="fileChosen" />
            @error('file')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="flex items-center gap-1" x-show="loading">
            <style>
                @keyframes loadingpulse {
                    0%   {transform: scale(.8); opacity: .75}
                    100% {transform: scale(1); opacity: .9}
                }
            </style>
            <span
                style="animation: loadingpulse 0.75s alternate infinite ease-in-out;"
                class="group w-8 h-8 inline-flex items-center justify-center bg-gradient-to-b from-blue-500 to-blue-600 text-white rounded-full">
                    <span class="flex items-center justify-center w-6 h-6 transform group-hover:scale-90 transition-transform duration-150">
                        @include('mailcoach::app.layouts.partials.logoSvg')
                    </span>
                </span>
            <span class="ml-1 text-gray-700">Uploading...</span>
        </div>
    </div>
    <template x-if="imageUrl">
        <div class="relative max-w-sm">
            <img :src="imageUrl"
                 class="object-cover rounded-md border border-gray-200 w-full card p-0"
            >
            <a href="" x-on:click.prevent="imageUrl = ''" class="link-danger mt-1 inline-block">
                {{ __mc('Remove image') }}
            </a>
        </div>
    </template>
</div>
