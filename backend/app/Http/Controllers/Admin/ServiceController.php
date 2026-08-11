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
            'singular' => 'Xizmat',
            'plural'   => 'Xizmatlar',
            'icon'     => 'briefcase',
            'hint'     => 'Xizmatlar sahifasidagi takliflar',
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'category', 'price'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Ikonka', 'type' => 'icon', 'value' => 'icon'],
            ['label' => 'Sarlavha', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Kategoriya', 'type' => 'badge', 'value' => 'category'],
            ['label' => 'Narx', 'type' => 'text', 'value' => 'price'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Sarlavha', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Tavsif', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            [
                'name' => 'category', 'label' => 'Kategoriya', 'type' => 'select', 'required' => true,
                'options' => [
                    'frontend'  => 'Frontend',
                    'backend'   => 'Backend',
                    'fullstack' => 'Full-Stack',
                    'mobile'    => 'Mobile',
                    'other'     => 'Boshqa',
                ],
            ],
            ['name' => 'icon', 'label' => 'Ikonka', 'type' => 'icon', 'required' => true],
            ['name' => 'price', 'label' => 'Narx', 'type' => 'text', 'placeholder' => '1 200 000+ UZS'],
            ['name' => 'tech', 'label' => 'Texnologiyalar', 'type' => 'list', 'required' => true],
            ['name' => 'features', 'label' => 'Imkoniyatlar', 'type' => 'trans_list', 'rows' => 5, 'required' => true, 'help' => 'Har bir imkoniyatni alohida qatorga yozing'],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0],
        ];
    }
}
