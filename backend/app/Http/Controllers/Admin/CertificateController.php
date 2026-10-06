<?php

namespace App\Http\Controllers\Admin;

use App\Models\Certificate;

class CertificateController extends ContentCrudController
{
    protected function model(): string
    {
        return Certificate::class;
    }

    protected function key(): string
    {
        return 'certificates';
    }

    protected function labels(): array
    {
        return [
            'singular' => 'Certificate',
            'plural'   => 'Certificates',
            'icon'     => 'award',
            'hint'     => 'Certificates, diplomas, awards and courses shown on the Certificates page',
        ];
    }

    protected function searchable(): array
    {
        return ['title', 'issuer', 'type'];
    }

    protected function columns(): array
    {
        return [
            ['label' => 'Image', 'type' => 'image', 'value' => 'image'],
            ['label' => 'Title', 'type' => 'trans', 'value' => 'title'],
            ['label' => 'Type', 'type' => 'badge', 'value' => 'type'],
            ['label' => 'Issued by', 'type' => 'text', 'value' => 'issuer'],
            ['label' => 'Date', 'type' => 'strong', 'value' => 'issued_at'],
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Title', 'type' => 'trans', 'required' => true],
            ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'required' => true, 'options' => Certificate::TYPES],
            ['name' => 'issuer', 'label' => 'Issued by', 'type' => 'text', 'placeholder' => 'Coursera, SamDU, IT Park…'],
            ['name' => 'issued_at', 'label' => 'Date issued', 'type' => 'date'],
            ['name' => 'description', 'label' => 'Description', 'type' => 'trans', 'textarea' => true, 'rows' => 3],
            ['name' => 'image', 'label' => 'Image', 'type' => 'image', 'help' => 'A photo or scan of the certificate; landscape works best'],
            ['name' => 'credential_url', 'label' => 'Link', 'type' => 'url', 'placeholder' => 'https://…', 'help' => 'Verification page, PDF or anything that proves it'],
            ['name' => 'sort_order', 'label' => 'Sort order', 'type' => 'number', 'min' => 0, 'help' => 'Lower numbers come first'],
        ];
    }
}
