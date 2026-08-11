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
            'singular' => 'Milestone',
            'plural'   => 'Journey',
            'icon'     => 'milestone',
            'hint'     => 'Experience year by year',
        ];
    }

    protected function searchable(): array
    {
        return ['year', 'title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Year', 'type' => 'strong', 'value' => 'year'],
            ['label' => 'Title', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Description', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'year', 'label' => 'Year', 'type' => 'text', 'required' => true, 'max' => 20, 'placeholder' => '2022'],
            ['name' => 'title', 'label' => 'Title', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
