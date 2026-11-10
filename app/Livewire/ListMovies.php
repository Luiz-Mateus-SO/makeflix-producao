<?php

namespace App\Livewire;

use App\Models\Film;
use Livewire\Component;

class ListMovies extends Component
{
    public int $perPage = 6;

    public function loadMore(): void
    {
        $search = trim((string) request()->query('search', ''));
        $selectedGenre = trim((string) request()->query('genre', ''));

        $totalFilms = Film::query()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%' . $search . '%'))
            ->when($selectedGenre !== '', fn ($query) => $query->where('genre', 'like', '%' . $selectedGenre . '%'))
            ->count();

        if ($this->perPage < $totalFilms) {
            $this->perPage += 6;
        }
    }

    public function toggleFavorite(int $filmId): void
    {
        $film = Film::findOrFail($filmId);
        $film->update(['is_favorite' => ! $film->is_favorite]);
    }

    public function deleteFilm(int $filmId): void
    {
        Film::findOrFail($filmId)->delete();
    }

    public function render()
    {
        $search = trim((string) request()->query('search', ''));
        $selectedGenre = trim((string) request()->query('genre', ''));

        $genres = Film::query()
            ->pluck('genre')
            ->flatMap(fn ($value) => array_filter(array_map('trim', explode(',', (string) $value))))
            ->unique()
            ->sort()
            ->values();

        $films = Film::latest()
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%' . $search . '%'))
            ->when($selectedGenre !== '', fn ($query) => $query->where('genre', 'like', '%' . $selectedGenre . '%'))
            ->take($this->perPage + 1)
            ->get();

        $hasMoreFilms = $films->count() > $this->perPage;
        $films = $films->take($this->perPage);

        $featuredFilm = $films->first();
        $relatedFilms = Film::latest()
            ->when($featuredFilm, fn ($query) => $query->whereKeyNot($featuredFilm->id))
            ->when($featuredFilm?->genre, function ($query) use ($featuredFilm) {
                $genres = array_filter(array_map('trim', explode(',', $featuredFilm->genre)));

                $query->where(function ($genreQuery) use ($genres) {
                    foreach ($genres as $genre) {
                        $genreQuery->orWhere('genre', 'like', '%' . $genre . '%');
                    }
                });
            })
            ->limit(12)
            ->get();

        if ($relatedFilms->isEmpty()) {
            $relatedFilms = Film::latest()
                ->when($featuredFilm, fn ($query) => $query->whereKeyNot($featuredFilm->id))
                ->limit(12)
                ->get();
        }

        return view('livewire.list-movies', [
            'films' => $films,
            'favoriteFilms' => Film::where('is_favorite', true)
                ->when($selectedGenre !== '', fn ($query) => $query->where('genre', 'like', '%' . $selectedGenre . '%'))
                ->latest()
                ->get(),
            'relatedFilms' => $relatedFilms,
            'search' => $search,
            'genres' => $genres,
            'selectedGenre' => $selectedGenre,
            'hasMoreFilms' => $hasMoreFilms,
        ]);
    }
}
