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
            'singular' => 'Project',
            'plural'   => 'Projects',
            'icon'     => 'folder-git-2',
            'hint'     => 'The work shown on the Portfolio page',
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'category', 'tech'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Image', 'type' => 'image', 'value' => 'image'],
            ['label' => 'Name', 'type' => 'trans', 'value' => 'name'],
            ['label' => 'Category', 'type' => 'badge', 'value' => 'category'],
            ['label' => 'Technologies', 'type' => 'tech', 'value' => 'tech'],
            ['label' => 'Featured', 'type' => 'bool', 'value' => 'featured'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Name', 'type' => 'trans', 'required' => true],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 4, 'required' => true],
            ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'help' => 'Recommended size: 1200×800px'],
            [
                'name' => 'category', 'label' => 'Category', 'type' => 'select', 'required' => true,
                'options' => [
                    'Full-Stack' => 'Full-Stack',
                    'Frontend'   => 'Frontend',
                    'Backend'    => 'Backend',
                    'Mobile'     => 'Mobile',
                    'Desktop'    => 'Desktop',
                ],
            ],
            ['name' => 'tech', 'label' => 'Technologies', 'type' => 'tech', 'required' => true, 'help' => 'Pick from the catalogue or type a new one'],
            ['name' => 'live_demo', 'label' => 'Live demo', 'type' => 'url', 'placeholder' => 'https://example.uz'],
            ['name' => 'github', 'label' => 'GitHub link', 'type' => 'url', 'placeholder' => 'https://github.com/…'],
            ['name' => 'featured', 'label' => 'Featured project', 'type' => 'bool', 'help' => 'Shown first on the home page'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0, 'help' => 'Lower numbers come first'],
        ];
    }
}
