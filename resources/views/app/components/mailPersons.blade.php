@if(count($persons))
    <div>
        {{ $label }}:<br/>
        @foreach($persons as $person)
            {{ $person['email'] }}
            @if ($person['name'])
                ({{ $person['name'] }})
            @endif
            <br/>
        @endforeach
    </div>
@endif
