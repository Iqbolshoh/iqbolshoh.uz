<?php

namespace App\Http\Controllers\Admin;

use App\Models\Beyond;

class BeyondController extends ContentCrudController
{
    protected function model(): string
    {
        return Beyond::class;
    }

    protected function key(): string
    {
        return 'beyonds';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Faoliyat',
            'plural'   => 'Dasturlashdan tashqari',
            'icon'     => 'heart-handshake',
            'hint'     => 'Ustozlik, ijtimoiy faoliyat va boshqa qiziqishlar',
        ];
    }

    protected function searchable(): array
    {
        return ['title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Ikonka', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Sarlavha', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Tavsif', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Sarlavha', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Tavsif', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            ['name' => 'icon', 'label' => 'Ikonka', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
