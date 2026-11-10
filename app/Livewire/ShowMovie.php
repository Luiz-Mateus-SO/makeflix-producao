<?php

namespace App\Livewire;

use App\Models\Film;
use Livewire\Component;

class ShowMovie extends Component
{
    public Film $film;
    public bool $playing = false;

    public function mount(Film $film): void
    {
        $this->film = $film;
        $this->playing = request()->boolean('play');
    }

    public function toggleFavorite(): void
    {
        $this->film->update(['is_favorite' => ! $this->film->is_favorite]);
        $this->film->refresh();
    }

    public function delete(): mixed
    {
        $this->film->delete();

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.show-movie');
    }
}
