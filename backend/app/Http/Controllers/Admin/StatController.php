<?php

namespace App\Http\Controllers\Admin;

use App\Models\Stat;

class StatController extends ContentCrudController
{
    protected function model(): string
    {
        return Stat::class;
    }

    protected function key(): string
    {
        return 'stats';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Stat',
            'plural'   => 'Stats',
            'icon'     => 'bar-chart-3',
            'hint'     => 'The numbers on the home page ("4+ years of experience")',
        ];
    }

    protected function searchable(): array
    {
        return ['value', 'label'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Icon', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Value', 'type' => 'strong', 'value' => 'value'],
            ['label' => 'Caption', 'type' => 'trans', 'value' => 'label'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'value', 'label' => 'Value', 'type' => 'text', 'required' => true, 'placeholder' => '4+'],
            ['name' => 'label', 'label' => 'Caption', 'type' => 'trans', 'required' => true],
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
