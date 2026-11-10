<?php

namespace App\Livewire;

use App\Models\Film;
use App\Services\TmdbService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class CreateMovie extends Component
{
    use WithFileUploads;

    public $title = '';
    public $summary = '';
    public $cover;

    public function deleteFilm(int $filmId): void
    {
        Film::findOrFail($filmId)->delete();
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'cover' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function save()
    {
        $validated = $this->validate();

        $title = trim($validated['title']);
        $normalizedTitle = mb_strtolower($title, 'UTF-8');

        if (Film::whereRaw('LOWER(title) = ?', [$normalizedTitle])->exists()) {
            $this->addError('title', 'Este filme já está cadastrado.');

            return;
        }

        $movieData = [
            'title' => $title,
            'summary' => trim((string) ($validated['summary'] ?? '')),
        ];

        if ($this->cover) {
            $movieData['cover'] = $this->cover->store('covers', 'public');
        } else {
            try {
                $results = app(TmdbService::class)->searchMovies($title);
                $tmdbMovie = collect($results)->first(function (array $movie) use ($normalizedTitle) {
                    return mb_strtolower($movie['title'] ?? '', 'UTF-8') === $normalizedTitle;
                }) ?? $results[0] ?? null;

                if (! $tmdbMovie) {
                    $this->addError('cover', 'Não encontramos esse filme no TMDB. Envie uma capa manualmente.');

                    return;
                }

                $details = app(TmdbService::class)->movieDetails((int) $tmdbMovie['id']);

                if (Film::where('tmdb_id', $details['id'])->exists()) {
                    $this->addError('title', 'Este filme já está cadastrado.');

                    return;
                }

                $translation = app(TmdbService::class)->portugueseTranslation($details);
                $movieData['title'] = $translation['title'] ?? ($details['title'] ?? $title);
                $movieData['original_title'] = $details['original_title'] ?? null;
                $movieData['title_language'] = $translation ? 'pt-BR' : 'original';
                $movieData['summary'] = $movieData['summary'] ?: ($translation['overview'] ?? $details['overview'] ?? '');
                $movieData['cover'] = isset($details['poster_path'])
                    ? 'https://image.tmdb.org/t/p/w780' . $details['poster_path']
                    : '';
                $movieData['backdrop'] = isset($details['backdrop_path'])
                    ? 'https://image.tmdb.org/t/p/w1280' . $details['backdrop_path']
                    : null;
                $movieData['tmdb_id'] = $details['id'] ?? null;
                $movieData['release_date'] = $details['release_date'] ?? null;
                $movieData['genre'] = collect($details['genres'] ?? [])
                    ->pluck('name')
                    ->implode(', ');
                $movieData['logo'] = app(TmdbService::class)->portugueseLogoUrl($details);
                $trailer = collect($details['videos']['results'] ?? [])
                    ->first(fn (array $video) => ($video['site'] ?? '') === 'YouTube' && ($video['type'] ?? '') === 'Trailer');
                $movieData['preview_video'] = $trailer['key'] ?? null;
            } catch (Throwable $exception) {
                $this->addError('title', 'Não foi possível consultar o TMDB. Configure o token ou envie capa e sinopse manualmente.');

                return;
            }
        }

        if (Film::whereRaw('LOWER(title) = ?', [mb_strtolower($movieData['title'], 'UTF-8')])->exists()) {
            $this->addError('title', 'Este filme já está cadastrado.');

            return;
        }

        if (blank($movieData['summary']) || blank($movieData['cover'])) {
            $this->addError('summary', 'Informe a sinopse e a capa ou use um título encontrado no TMDB.');

            return;
        }

        $movie = Film::create($movieData);

        return redirect()->route('see', $movie);
    }

    public function render()
    {
        return view('livewire.create-movie', [
            'films' => Film::latest()->get(),
        ]);
    }
}
