<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteContent;
use App\Support\SiteIcons;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Shared CRUD for every site content section.
 *
 * A section (projects, services, stats, …) only declares its specification —
 * the table columns and the form fields. Everything else lives here: search,
 * ordering, validation, image uploads and cache invalidation. That is what
 * keeps eight nearly identical controllers from being eight copies of the
 * same two hundred lines.
 *
 * Field types:
 *   trans       — multilingual text ({"uz":…,"en":…,"ru":…,"tj":…})
 *   trans_list  — multilingual list, one entry per line for each language
 *   list        — plain list (["Laravel","React"])
 *   text | textarea | url | number | date | bool | select | icon | image
 */
abstract class ContentCrudController extends Controller
{
    /** Eloquent model class this section edits. */
    abstract protected function model(): string;

    /**
     * Section key. Both the route names and the permissions derive from it:
     * "projects" → route('admin.projects.index'), permission "projects.view".
     */
    abstract protected function key(): string;

    /** ['singular' => 'Loyiha', 'plural' => 'Loyihalar', 'icon' => 'folder-git-2'] */
    abstract protected function labels(): array;

    /** Columns rendered in the listing table. */
    abstract protected function columns(): array;

    /** Fields rendered in the create/edit form. */
    abstract protected function fields(): array;

    /** Columns the search box looks through. */
    protected function searchable(): array
    {
        return [];
    }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->authorizeAction('view');

        $model = $this->model();
        $query = $model::query();

        if ($search = trim((string) $request->input('search'))) {
            $columns = $this->searchable();
            $query->where(function ($q) use ($columns, $search) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $items = $query->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString();

        return view('admin.content.index', $this->viewData(['items' => $items]));
    }

    public function create()
    {
        $this->authorizeAction('create');

        $model = $this->model();

        return view('admin.content.form', $this->viewData([
            'item'   => new $model(),
            'action' => route("admin.{$this->key()}.store"),
            'method' => 'POST',
        ]));
    }

    public function store(Request $request)
    {
        $this->authorizeAction('create');

        $model = $this->model();
        $model::create($this->payload($request, new $model()));

        SiteContent::flush();

        return redirect()->route("admin.{$this->key()}.index")
            ->with('success', $this->labels()['singular'] . ' qo\'shildi.');
    }

    public function edit(int $id)
    {
        $this->authorizeAction('edit');

        $model = $this->model();
        $item  = $model::findOrFail($id);

        return view('admin.content.form', $this->viewData([
            'item'   => $item,
            'action' => route("admin.{$this->key()}.update", $item->id),
            'method' => 'PUT',
        ]));
    }

    public function update(Request $request, int $id)
    {
        $this->authorizeAction('edit');

        $model = $this->model();
        $item  = $model::findOrFail($id);

        $item->update($this->payload($request, $item));

        SiteContent::flush();

        return redirect()->route("admin.{$this->key()}.index")
            ->with('success', $this->labels()['singular'] . ' saqlandi.');
    }

    public function destroy(int $id)
    {
        $this->authorizeAction('delete');

        $model = $this->model();
        $item  = $model::findOrFail($id);

        $this->deleteUploadedImages($item);
        $item->delete();

        SiteContent::flush();

        return redirect()->route("admin.{$this->key()}.index")
            ->with('success', $this->labels()['singular'] . ' o\'chirildi.');
    }

    // ── Internals ────────────────────────────────────────────────────────────

    protected function authorizeAction(string $action): void
    {
        abort_unless(Auth::user()?->can("{$this->key()}.{$action}"), 403);
    }

    /** Data every content view needs. */
    protected function viewData(array $extra = []): array
    {
        return array_merge([
            'key'     => $this->key(),
            'labels'  => $this->labels(),
            'columns' => $this->columns(),
            'fields'  => $this->fields(),
        ], $extra);
    }

    /** Validate the request and turn it into the model's attribute array. */
    protected function payload(Request $request, Model $item): array
    {
        $rules      = [];
        $attributes = [];

        foreach ($this->fields() as $field) {
            $name     = $field['name'];
            $required = $field['required'] ?? false;

            switch ($field['type']) {
                case 'trans':
                case 'trans_list':
                    // Only the primary language is mandatory. Blank translations
                    // are filled from it below, so the site never renders an
                    // empty string just because one tab was left untouched.
                    $rules["{$name}." . $this->defaultLocale()] = $required ? ['required', 'string'] : ['nullable', 'string'];

                    foreach (array_keys(SiteContent::LOCALES) as $locale) {
                        $rules["{$name}.{$locale}"] ??= ['nullable', 'string'];
                        $attributes["{$name}.{$locale}"] = $field['label'] . ' (' . SiteContent::LOCALES[$locale] . ')';
                    }
                    break;

                case 'image':
                    $rules["{$name}_file"]      = ['nullable', 'image', 'max:4096'];
                    $attributes["{$name}_file"] = $field['label'];
                    break;

                case 'number':
                    $rule = [$required ? 'required' : 'nullable', 'integer'];

                    if (isset($field['min'])) {
                        $rule[] = 'min:' . $field['min'];
                    }

                    if (isset($field['max'])) {
                        $rule[] = 'max:' . $field['max'];
                    }

                    $rules[$name] = $rule;
                    break;

                case 'url':
                    $rules[$name] = [$required ? 'required' : 'nullable', 'url', 'max:255'];
                    break;

                case 'date':
                    $rules[$name] = [$required ? 'required' : 'nullable', 'date'];
                    break;

                case 'bool':
                    $rules[$name] = ['nullable', 'boolean'];
                    break;

                case 'icon':
                    $rules[$name] = [$required ? 'required' : 'nullable', 'string', 'in:' . implode(',', SiteIcons::NAMES)];
                    break;

                case 'select':
                    $rules[$name] = [$required ? 'required' : 'nullable', 'string', 'in:' . implode(',', array_keys($field['options']))];
                    break;

                case 'list':
                    $rules[$name] = [$required ? 'required' : 'nullable', 'string'];
                    break;

                default: // text, textarea
                    $rules[$name] = array_merge(
                        [$required ? 'required' : 'nullable', 'string', 'max:' . ($field['max'] ?? 255)],
                        $field['rules'] ?? []
                    );
            }

            $attributes[$name] ??= $field['label'];
        }

        $validated = $request->validate($rules, [], $attributes);

        $data = [];

        foreach ($this->fields() as $field) {
            $name = $field['name'];

            switch ($field['type']) {
                case 'trans':
                    $data[$name] = $this->fillLocales($validated[$name] ?? []);
                    break;

                case 'trans_list':
                    $data[$name] = array_map(
                        fn($text) => $this->splitLines($text),
                        $this->fillLocales($validated[$name] ?? [])
                    );
                    break;

                case 'list':
                    $data[$name] = $this->splitLines($validated[$name] ?? '');
                    break;

                case 'bool':
                    $data[$name] = $request->boolean($name);
                    break;

                case 'image':
                    $data[$name] = $this->resolveImage($request, $item, $name);
                    break;

                case 'number':
                    $data[$name] = $validated[$name] ?? ($field['default'] ?? 0);
                    break;

                default:
                    $data[$name] = $validated[$name] ?? null;
            }
        }

        return $data;
    }

    protected function defaultLocale(): string
    {
        return array_key_first(SiteContent::LOCALES);
    }

    /** Copy the primary language into any translation left blank. */
    protected function fillLocales(array $values): array
    {
        $fallback = trim((string) ($values[$this->defaultLocale()] ?? ''));

        $filled = [];

        foreach (array_keys(SiteContent::LOCALES) as $locale) {
            $value           = trim((string) ($values[$locale] ?? ''));
            $filled[$locale] = $value !== '' ? $value : $fallback;
        }

        return $filled;
    }

    /** Accepts either "Laravel, React" or one entry per line. */
    protected function splitLines(?string $text): array
    {
        $parts = preg_split('/[\r\n,]+/', (string) $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn($part) => $part !== ''));
    }

    /**
     * Store a newly uploaded file, honour the "remove" checkbox, and otherwise
     * keep whatever the record already points at.
     */
    protected function resolveImage(Request $request, Model $item, string $name): ?string
    {
        $current = $item->{$name};

        if ($request->boolean("{$name}_remove")) {
            $this->deleteFile($current);

            return null;
        }

        if ($request->hasFile("{$name}_file")) {
            $file = $request->file("{$name}_file");
            $slug = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'image';

            $path = $file->storeAs(
                "media/{$this->key()}",
                $slug . '-' . Str::lower(Str::random(6)) . '.' . $file->getClientOriginalExtension(),
                'public'
            );

            $this->deleteFile($current);

            // The site consumes this as a plain URL; nginx serves `/media/`
            // straight from the public disk.
            return '/' . $path;
        }

        return $current;
    }

    protected function deleteUploadedImages(Model $item): void
    {
        foreach ($this->fields() as $field) {
            if ($field['type'] === 'image') {
                $this->deleteFile($item->{$field['name']});
            }
        }
    }

    /** Only admin uploads are removable; `/images/…` belongs to the frontend build. */
    protected function deleteFile(?string $path): void
    {
        if ($path && str_starts_with($path, '/media/')) {
            Storage::disk('public')->delete(ltrim($path, '/'));
        }
    }
}
