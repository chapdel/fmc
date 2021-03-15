<div class="form-field">
    <p>You can run the automation manually using code:</p>
    <pre class="max-w-full overflow-x-auto bg-blue-100 p-2">
<code class="">// A single Subscriber instance
$automation->run($subscriber);

// A collection or array containing Subscribers
$automation->run($subscribers);

// A query builder instance
$automation->run($automation->emailList->subscribers());
</code></pre>
    <p class="my-4">The automation will only run for subscribed subscribers of the automation's email list & segment.</p>
</div>
