<?php

namespace App\Services;

use App\Models\MenuItem;
use Illuminate\Support\Collection;

class MenuService
{
    public function getMenu(): Collection
    {
        return MenuItem::active()
            ->root()
            ->orderBy('order')
            ->with('children')
            ->get()
            ->map(fn($item) => $this->formatMenuItem($item));
    }

    private function formatMenuItem($item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'route' => $item->route,
            'icon' => $item->icon,
            'children' => $item->children->map(fn($child) => $this->formatMenuItem($child))->toArray(),
        ];
    }
}
