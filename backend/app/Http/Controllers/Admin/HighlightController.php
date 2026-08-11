<?php

namespace App\Http\Controllers\Admin;

use App\Models\Highlight;

class HighlightController extends ContentCrudController
{
    protected function model(): string
    {
        return Highlight::class;
    }

    protected function key(): string
    {
        return 'highlights';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Highlight',
            'plural'   => 'Highlights',
            'icon'     => 'sparkles',
            'hint'     => 'Short facts on the About page',
        ];
    }

    protected function searchable(): array
    {
        return ['text'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Icon', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Text', 'type' => 'trans', 'value' => 'text'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'text', 'label' => 'Text', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
