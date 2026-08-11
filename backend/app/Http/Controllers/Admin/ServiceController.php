<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;

class ServiceController extends ContentCrudController
{
    protected function model(): string
    {
        return Service::class;
    }

    protected function key(): string
    {
        return 'services';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Service',
            'plural'   => 'Services',
            'icon'     => 'briefcase',
            'hint'     => 'The offers on the Services page',
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'category', 'price'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Icon', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Title', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Category', 'type' => 'badge', 'value' => 'category'],
            ['label' => 'Price', 'type' => 'text', 'value' => 'price'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            [
                'name' => 'category', 'label' => 'Category', 'type' => 'select', 'required' => true,
                'options' => [
                    'frontend'  => 'Frontend',
                    'backend'   => 'Backend',
                    'fullstack' => 'Full-Stack',
                    'mobile'    => 'Mobile',
                    'other'     => 'Boshqa',
                ],
            ],
            ['name' => 'icon', 'label' => 'Icon', 'type' => 'icon', 'required' => true],
            ['name' => 'price', 'label' => 'Price', 'type' => 'text', 'placeholder' => '1 200 000+ UZS'],
            ['name' => 'tech', 'label' => 'Technologies', 'type' => 'tech', 'required' => true],
            ['name' => 'features', 'label' => 'Features', 'type' => 'trans_list', 'rows' => 5, 'required' => true, 'help' => 'One feature per line'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0],
        ];
    }
}
