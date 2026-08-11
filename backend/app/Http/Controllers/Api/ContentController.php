<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beyond;
use App\Models\Highlight;
use App\Models\Journey;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\TechStack;
use Illuminate\Support\Facades\Cache;

/**
 * Sayt kontenti API.
 *
 * Javob shakli eski `src/data/content.ts` bilan bir xil saqlangan,
 * shuning uchun frontend komponentlari o'zgarishsiz ishlaydi.
 */
class ContentController extends Controller
{
    private const CACHE_KEY = 'site.content';
    private const CACHE_TTL = 60;

    /** Butun kontent bitta so'rovda. */
    public function index()
    {
        return response()->json(
            Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn() => $this->payload())
        );
    }

    public function projects()
    {
        return response()->json($this->projectList());
    }

    public function services()
    {
        return response()->json($this->serviceList());
    }

    /**
     * DIQQAT: bu yerda faqat oddiy massiv qaytarilishi shart.
     * Eloquent modellari kesh'ga serialize qilinganda `__PHP_Incomplete_Class`
     * bo'lib qaytadi, shuning uchun hamma joyda `toArray()` ishlatilgan.
     */
    private function payload(): array
    {
        return [
            'personalInfo' => SiteSetting::pluck('value', 'key')->all(),
            'techStack'    => TechStack::orderBy('sort_order')
                ->get(['name', 'icon', 'level'])
                ->toArray(),
            'stats'        => Stat::orderBy('sort_order')
                ->get(['icon', 'value', 'label'])
                ->toArray(),
            'projects'     => $this->projectList(),
            'highlights'   => Highlight::orderBy('sort_order')
                ->get(['icon', 'text'])
                ->toArray(),
            'journey'      => Journey::orderBy('sort_order')
                ->get(['year', 'title', 'description'])
                ->toArray(),
            'beyond'       => Beyond::orderBy('sort_order')
                ->get(['icon', 'title', 'description'])
                ->toArray(),
            'services'     => $this->serviceList(),
            'processSteps' => ProcessStep::orderBy('sort_order')
                ->get(['step', 'title', 'description'])
                ->toArray(),
        ];
    }

    private function projectList(): array
    {
        return Project::orderBy('sort_order')->get()->map(fn(Project $p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'description' => $p->description,
            'image'       => $p->image,
            'tech'        => $p->tech,
            'liveDemo'    => $p->live_demo,
            'github'      => $p->github,
            'featured'    => $p->featured,
            'category'    => $p->category,
        ])->all();
    }

    private function serviceList(): array
    {
        return Service::orderBy('sort_order')->get()->map(fn(Service $s) => [
            'id'          => $s->id,
            'category'    => $s->category,
            'icon'        => $s->icon,
            'price'       => $s->price,
            'title'       => $s->title,
            'description' => $s->description,
            'tech'        => $s->tech,
            'features'    => $s->features,
        ])->all();
    }

}
