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
            'singular' => 'Step',
            'plural'   => 'Process',
            'icon'     => 'list-checks',
            'hint'     => 'The "how I work" steps on the Services page',
        ];
    }

    protected function searchable(): array
    {
        return ['step', 'title'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Step', 'type' => 'strong', 'value' => 'step'],
            ['label' => 'Title', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Description', 'type' => 'trans', 'value' => 'description'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'step', 'label' => 'Step number', 'type' => 'text', 'required' => true, 'max' => 10, 'placeholder' => '01'],
            ['name' => 'title', 'label' => 'Title', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 3, 'required' => true],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
