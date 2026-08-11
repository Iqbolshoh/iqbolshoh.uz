<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;

class ProjectController extends ContentCrudController
{
    protected function model(): string
    {
        return Project::class;
    }

    protected function key(): string
    {
        return 'projects';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Loyiha',
            'plural'   => 'Loyihalar',
            'icon'     => 'folder-git-2',
            'hint'     => 'Portfolio sahifasidagi ishlar',
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'category', 'tech'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Rasm', 'type' => 'image', 'value' => 'image'],
            ['label' => 'Nomi', 'type' => 'trans', 'value' => 'name'],
            ['label' => 'Kategoriya', 'type' => 'badge', 'value' => 'category'],
            ['label' => 'Texnologiyalar', 'type' => 'list', 'value' => 'tech'],
            ['label' => 'Tanlangan', 'type' => 'bool', 'value' => 'featured'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nomi', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Tavsif', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            ['name' => 'image', 'label' => 'Rasm', 'type' => 'image', 'help' => 'Tavsiya etilgan o\'lcham: 1200×800px'],
            [
                'name' => 'category', 'label' => 'Kategoriya', 'type' => 'select', 'required' => true,
                'options' => [
                    'Full-Stack' => 'Full-Stack',
                    'Frontend'   => 'Frontend',
                    'Backend'    => 'Backend',
                    'Mobile'     => 'Mobile',
                    'Desktop'    => 'Desktop',
                ],
            ],
            ['name' => 'tech', 'label' => 'Texnologiyalar', 'type' => 'list', 'required' => true, 'help' => 'Har birini vergul yoki yangi qatorda yozing'],
            ['name' => 'live_demo', 'label' => 'Jonli havola', 'type' => 'url', 'placeholder' => 'https://example.uz'],
            ['name' => 'github', 'label' => 'GitHub havolasi', 'type' => 'url', 'placeholder' => 'https://github.com/…'],
            ['name' => 'featured', 'label' => 'Tanlangan loyiha', 'type' => 'bool', 'help' => 'Bosh sahifada birinchi bo\'lib ko\'rsatiladi'],
            ['name' => 'sort_order', 'label' => 'Tartib raqami', 'type' => 'number', 'min' => 0, 'help' => 'Kichik raqam yuqorida turadi'],
        ];
    }
}
