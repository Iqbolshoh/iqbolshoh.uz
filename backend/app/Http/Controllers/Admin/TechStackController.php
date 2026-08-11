<?php

namespace App\Http\Controllers\Admin;

use App\Models\TechStack;

class TechStackController extends ContentCrudController
{
    protected function model(): string
    {
        return TechStack::class;
    }

    protected function key(): string
    {
        return 'tech-stacks';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Texnologiya',
            'plural'   => 'Texnologiyalar',
            'icon'     => 'layers',
            'hint'     => 'Ko\'nikmalar bo\'limidagi daraja ko\'rsatkichlari',
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Ikonka', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Nomi', 'type' => 'text', 'value' => 'name'],
            ['label' => 'Daraja', 'type' => 'meter', 'value' => 'level'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nomi', 'type' => 'text', 'required' => true, 'placeholder' => 'Laravel'],
            ['name' => 'icon', 'label' => 'Ikonka', 'type' => 'icon', 'required' => true],
            ['name' => 'level', 'label' => 'Daraja (%)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 100],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
