<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

trait InteractsWithTable
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'created_at')]
    public string $sortBy = 'created_at';

    #[Url(as: 'dir', except: 'desc')]
    public string $sortDirection = 'desc';

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * @param  Builder<Model>  $query
     * @param  list<string>  $allowedColumns
     * @return Builder<Model>
     */
    protected function applySorting($query, array $allowedColumns, string $default = 'created_at')
    {
        $column = in_array($this->sortBy, $allowedColumns, true) ? $this->sortBy : $default;
        $direction = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $direction);
    }
}
