<?php

namespace App\Http\Controllers\Admin;

use App\Models\Journey;

class JourneyController extends ContentCrudController
{
    protected function model(): string
    {
        return Journey::class;
    }

    protected function key(): string
    {
        return 'journeys';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Bosqich',
            'plural'   => 'Yo\'l bosqichlari',
            'icon'     => 'milestone',
            'hint'     => 'Yillar bo\'yicha tajriba yo\'li',
        ];
    }

    protected function searchable(): array
    {
        return ['year', 'title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Yil', 'type' => 'strong', 'value' => 'year'],
            ['label' => 'Sarlavha', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Tavsif', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'year', 'label' => 'Yil', 'type' => 'text', 'required' => true, 'max' => 20, 'placeholder' => '2022'],
            ['name' => 'title', 'label' => 'Sarlavha', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Tavsif', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
