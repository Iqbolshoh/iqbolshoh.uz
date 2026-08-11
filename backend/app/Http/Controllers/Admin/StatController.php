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
            'singular' => 'Ko\'rsatkich',
            'plural'   => 'Ko\'rsatkichlar',
            'icon'     => 'bar-chart-3',
            'hint'     => 'Bosh sahifadagi raqamlar ("4+ yillik tajriba")',
        ];
    }

    protected function searchable(): array
    {
        return ['value', 'label'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Ikonka', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Qiymat', 'type' => 'strong', 'value' => 'value'],
            ['label' => 'Izoh', 'type' => 'trans', 'value' => 'label'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'value', 'label' => 'Qiymat', 'type' => 'text', 'required' => true, 'placeholder' => '4+'],
            ['name' => 'label', 'label' => 'Izoh', 'type' => 'trans', 'required' => true],
            ['name' => 'icon', 'label' => 'Ikonka', 'type' => 'icon', 'required' => true],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
