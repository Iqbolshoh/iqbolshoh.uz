<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProcessStep;

class ProcessStepController extends ContentCrudController
{
    protected function model(): string
    {
        return ProcessStep::class;
    }

    protected function key(): string
    {
        return 'process-steps';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Bosqich',
            'plural'   => 'Ish jarayoni',
            'icon'     => 'list-checks',
            'hint'     => 'Xizmatlar sahifasidagi "qanday ishlayman" bosqichlari',
        ];
    }

    protected function searchable(): array
    {
        return ['step', 'title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Qadam', 'type' => 'strong', 'value' => 'step'],
            ['label' => 'Sarlavha', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Tavsif', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'step', 'label' => 'Qadam raqami', 'type' => 'text', 'required' => true, 'max' => 10, 'placeholder' => '01'],
            ['name' => 'title', 'label' => 'Sarlavha', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Tavsif', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
