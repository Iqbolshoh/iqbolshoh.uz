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
            'singular' => 'Ta\'kid',
            'plural'   => 'Ta\'kidlar',
            'icon'     => 'sparkles',
            'hint'     => '"Men haqimda" sahifasidagi qisqa faktlar',
        ];
    }

    protected function searchable(): array
    {
        return ['text'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Ikonka', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Matn', 'type' => 'trans', 'value' => 'text'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'text', 'label' => 'Matn', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'icon', 'label' => 'Ikonka', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
