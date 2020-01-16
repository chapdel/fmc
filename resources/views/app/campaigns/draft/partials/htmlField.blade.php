<div class="form-row max-w-full">
    @error('html')
        <p class="form-error" role="alert">{{ $message }}</p>
    @enderror
    <label class="label label-required" for="html">Body (HTML)</label>
    <textarea class="input input-html" required rows="20" id="html" name="html" data-html-preview-source>{{ old('html', $campaign->html) }}</textarea>
</div>
