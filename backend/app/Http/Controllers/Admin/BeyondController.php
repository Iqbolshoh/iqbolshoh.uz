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
            'singular' => 'Activity',
            'plural'   => 'Beyond code',
            'icon'     => 'heart-handshake',
            'hint'     => 'Mentoring, community work and other interests',
        ];
    }

    protected function searchable(): array
    {
        return ['title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Icon', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Title', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Description', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
