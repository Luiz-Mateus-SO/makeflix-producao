<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TmdbService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.tmdb.base_url'), '/');
    }

    public function searchMovies(string $query): array
    {
        return $this->request('/search/movie', [
            'query' => $query,
            'language' => 'pt-BR',
            'include_adult' => false,
        ])['results'] ?? [];
    }

    public function movieDetails(int $movieId): array
    {
        return $this->request("/movie/{$movieId}", [
            'language' => 'pt-BR',
            'append_to_response' => 'videos,images,translations',
            'include_image_language' => 'pt,null',
        ]);
    }

    public function portugueseLogoUrl(array $movie): ?string
    {
        $logo = collect($movie['images']['logos'] ?? [])
            ->first(fn (array $image) => ($image['iso_639_1'] ?? null) === 'pt');

        return isset($logo['file_path'])
            ? 'https://image.tmdb.org/t/p/w500' . $logo['file_path']
            : null;
    }

    public function portugueseTranslation(array $movie): ?array
    {
        $translation = collect($movie['translations']['translations'] ?? [])
            ->first(fn (array $translation) => ($translation['iso_639_1'] ?? null) === 'pt'
                && ($translation['iso_3166_1'] ?? null) === 'BR');

        return $translation['data'] ?? null;
    }

    private function request(string $path, array $query = []): array
    {
        $token = config('services.tmdb.token');

        if (blank($token)) {
            throw new RuntimeException('TMDB_ACCESS_TOKEN não foi configurado no arquivo .env.');
        }

        $response = Http::acceptJson()
            ->withToken($token)
            ->get($this->baseUrl . $path, $query);

        $response->throw();

        return $response->json();
    }
}
