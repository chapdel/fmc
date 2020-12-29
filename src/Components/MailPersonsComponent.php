<?php

namespace Spatie\Mailcoach\Components;

use Illuminate\View\Component;

class MailPersonsComponent extends Component
{
    public string $label;

    public array $persons;

    public function __construct(string $label, array $persons)
    {
        $this->label = $label;

        $this->persons = $persons;
    }

    public function render()
    {
        return view('mailcoach::app.components.mailPersons');
    }
}
