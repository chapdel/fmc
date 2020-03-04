<div>
    <c-html-field label="Body (HTML)" name="html" :value="old('html', $html)"></c-html-field>
</div>

<div class="form-buttons">
    <button id="save" type="submit" class="button">
        <c-icon-label icon="fa-code" text="Save content"/>
    </button>

    <button type="button" class="link-icon" data-modal-trigger="preview">
        <c-icon-label icon="fa-eye" text="Preview"/>
    </button>
    <c-modal title="Preview" name="preview" large>
        <iframe class="absolute" width="100%" height="100%" data-html-preview-target></iframe>
    </c-modal>
</div>

<c-replacer-help-texts />
