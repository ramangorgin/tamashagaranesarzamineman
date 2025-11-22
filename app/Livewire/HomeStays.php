<?php

namespace App\Livewire;

use App\Models\Stay;
use Livewire\Component;
use Livewire\WithPagination;

class HomeStays extends Component
{
    use WithPagination;

    // Filters / state
    public string $search = '';
    public string $province = '';
    public string $city = '';
    public string $sort = 'latest'; // latest | price_low | price_high
    public int $perPage = 8;

    protected $queryString = [
        'search' => ['except' => ''],
        'province' => ['except' => ''],
        'city' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    protected $listeners = [
        'refreshHomeStays' => '$refresh',
    ];

    public function updating($name): void
    {
        if (in_array($name, ['search','province','city','sort'])) {
            $this->resetPage();
        }
    }

    public function loadMore(): void
    {
        $this->perPage += 8;
    }

    public function getStaysQuery()
    {
        $q = Stay::query()
            ->with(['images'])
            ->where('is_active', true)
            ->where(function($q){
                // If moderation_status column exists and approved stays store a value like 'approved'
                if (\Schema::hasColumn('stays','moderation_status')) {
                    $q->where('moderation_status','approved');
                }
            });

        if ($this->search !== '') {
            $q->where(function($qq){
                $term = '%'.$this->search.'%';
                $qq->where('title','like',$term)
                   ->orWhere('province_name','like',$term)
                   ->orWhere('city_name','like',$term);
            });
        }
        if ($this->province !== '') {
            $q->where('province_name',$this->province);
        }
        if ($this->city !== '') {
            $q->where('city_name',$this->city);
        }

        switch ($this->sort) {
            case 'price_low':
                $q->orderBy('price_per_person','asc');
                break;
            case 'price_high':
                $q->orderBy('price_per_person','desc');
                break;
            default: // latest
                $q->latest();
        }

        return $q->take($this->perPage);
    }

    public function render()
    {
        $stays = $this->getStaysQuery()->get();
        return view('livewire.home-stays', [
            'stays' => $stays,
        ]);
    }
}
