<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AnimeController extends Controller
{
    private string $base;

    public function __construct()
    {
        $this->base = config('app.jikan_base', env('JIKAN_BASE', 'https://api.jikan.moe/v4'));
    }

    // GET /api/anime/search?q=naruto&page=1
    public function search(Request $r)
    {
        $q = trim($r->query('q', ''));
        $page = max(1, (int)$r->query('page', 1));
        if ($q === '') {
            return response()->json(['message' => 'Parameter q wajib diisi'], 422);
        }

        $cacheKey = "anime_search:{$q}:{$page}";
        $payload = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($q, $page) {
            $res = Http::retry(2, 200)
                ->timeout(10)
                ->get("{$this->base}/anime", ['q' => $q, 'page' => $page]);

            if ($res->failed()) {
                abort($res->status(), $res->json('message') ?? 'Failed fetching Jikan');
            }
            return $this->normalizeList($res->json());
        });

        return response()->json($payload);
    }

    // GET /api/anime/top?page=1
    public function top(Request $r)
    {
        $page = max(1, (int)$r->query('page', 1));
        $cacheKey = "anime_top:{$page}";
        $payload = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($page) {
            $res = Http::retry(2, 200)
                ->timeout(10)
                ->get("{$this->base}/top/anime", ['page' => $page]);
            if ($res->failed()) {
                abort($res->status(), $res->json('message') ?? 'Failed fetching Jikan');
            }
            return $this->normalizeList($res->json());
        });

        return response()->json($payload);
    }

    // GET /api/anime/{mal_id}
    public function show($mal_id)
    {
        $cacheKey = "anime_show:{$mal_id}";
        $payload = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($mal_id) {
            $res = Http::retry(2, 200)
                ->timeout(10)
                ->get("{$this->base}/anime/{$mal_id}");
            if ($res->failed()) {
                abort($res->status(), $res->json('message') ?? 'Failed fetching Jikan');
            }
            $json = $res->json();
            return $this->normalizeDetail($json['data'] ?? []);
        });

        return response()->json($payload);
    }

    // === Helpers ===
    private function normalizeList(array $json): array
    {
        $items = $json['data'] ?? [];
        $data = array_map(fn ($a) => [
            'id'         => $a['mal_id'] ?? null,
            'title'      => $a['title'] ?? null,
            'title_jp'   => $a['title_japanese'] ?? null,
            'type'       => $a['type'] ?? null,
            'episodes'   => $a['episodes'] ?? null,
            'score'      => $a['score'] ?? null,
            'year'       => $a['year'] ?? null,
            'status'     => $a['status'] ?? null,
            'genres'     => array_map(fn($g)=>$g['name'],$a['genres'] ?? []),
            'cover'      => $a['images']['jpg']['large_image_url'] ?? ($a['images']['webp']['large_image_url'] ?? null),
            'url'        => $a['url'] ?? null,
        ], $items);

        $pagination = $json['pagination'] ?? [];
        return [
            'data' => $data,
            'pagination' => [
                'last_visible_page' => $pagination['last_visible_page'] ?? 1,
                'has_next_page'     => $pagination['has_next_page'] ?? false,
                'current_page'      => $pagination['current_page'] ?? null,
                'items'             => $pagination['items'] ?? null,
            ],
            'source' => 'jikan.moe',
        ];
    }

    private function normalizeDetail(array $a): array
    {
        return [
            'id'         => $a['mal_id'] ?? null,
            'title'      => $a['title'] ?? null,
            'titles'     => $a['titles'] ?? [],
            'synopsis'   => $a['synopsis'] ?? null,
            'background' => $a['background'] ?? null,
            'type'       => $a['type'] ?? null,
            'episodes'   => $a['episodes'] ?? null,
            'duration'   => $a['duration'] ?? null,
            'rating'     => $a['rating'] ?? null,
            'score'      => $a['score'] ?? null,
            'rank'       => $a['rank'] ?? null,
            'popularity' => $a['popularity'] ?? null,
            'year'       => $a['year'] ?? null,
            'status'     => $a['status'] ?? null,
            'genres'     => array_map(fn($g)=>$g['name'],$a['genres'] ?? []),
            'studios'    => array_map(fn($s)=>$s['name'],$a['studios'] ?? []),
            'cover'      => $a['images']['jpg']['large_image_url'] ?? ($a['images']['webp']['large_image_url'] ?? null),
            'trailer'    => $a['trailer']['url'] ?? null,
            'url'        => $a['url'] ?? null,
        ];
    }
}
