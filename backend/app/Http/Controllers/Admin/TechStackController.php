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
            'singular' => 'Technology',
            'plural'   => 'Technologies',
            'icon'     => 'layers',
            'hint'     => 'The skill meters in the Skills section',
        ];
    }

    protected function searchable(): array
    {
        return ['name'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Icon', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Name', 'type' => 'text', 'value' => 'name'],
            ['label' => 'Level', 'type' => 'meter', 'value' => 'level'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Laravel'],
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'required' => true],
            ['name' => 'level', 'label' => 'Level (%)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 100],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
