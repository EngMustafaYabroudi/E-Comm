<?php

namespace App\Http\Livewire\User;

use Livewire\Component;

class UserDashboerdComponent extends Component
{
    public function render()
    {
        return view('livewire.user.user-dashboerd-component')->layout('layouts.base');
    }
}
