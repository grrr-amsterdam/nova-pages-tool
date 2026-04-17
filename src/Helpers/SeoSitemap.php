<?php

namespace Grrr\Pages\Helpers;

use Carbon\Carbon;

class SeoSitemap
{
    private array $items = [];
    private bool $use_lastmod;

    public function __construct(bool $use_lastmod = true)
    {
        $this->use_lastmod = $use_lastmod;
        $sitemap_models = config('seo.sitemap_models', []);
        $this->attachModelItems($sitemap_models);
    }

    private function attachModelItems(array $sitemap_models = []): void
    {
        foreach ($sitemap_models as $sitemap_model) {
            $items = $sitemap_model::getSitemapItems();
            if ($items && $items->count() > 0) {
                $this->items = array_merge(
                    $this->items,
                    $items
                        ->map(
                            fn($item) => (object) [
                                'url' => $item->getSitemapItemUrl(),
                                'lastmod' => $item->getSitemapItemLastModified(),
                            ]
                        )
                        ->toArray()
                );
            }
        }
    }

    public function attachCustom(string $path, ?string $lastmod = null): static
    {
        $this->items[] = (object) [
            'url' => url($path),
            'lastmod' => $lastmod,
        ];
        return $this;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function toXml(): string
    {
        $xml =
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        $lastmod = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($this->items as $item) {
            $use_lastmod = $this->use_lastmod
                ? $item->lastmod ?? $lastmod
                : null;
            $xml .=
                '<url>' .
                '<loc>' .
                (str_starts_with($item->url, '/')
                    ? url($item->url)
                    : $item->url) .
                '</loc>' .
                ($use_lastmod
                    ? '<lastmod>' . $use_lastmod . '</lastmod>'
                    : '') .
                '</url>';
            if ($item->lastmod) {
                $lastmod = $item->lastmod;
            }
        }
        $xml .= '</urlset>';
        return $xml;
    }
}
