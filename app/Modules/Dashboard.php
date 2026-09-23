<?php

namespace App\Modules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Figures and charts shown on the admin panel home, contributed by the core
 * and modules. Values are closures so nothing is queried unless the
 * dashboard is rendered and the item is visible to the user.
 */
class Dashboard
{
    private array $stats = [];

    private array $charts = [];

    /**
     * @param  string  $label  translation key or plain text
     * @param  string  $icon  Tabler icon name, e.g. "users" for "ti ti-users"
     * @param  Closure(): int  $value
     * @param  string|null  $route  page listing the counted records
     * @param  int[]  $roles  allowed user roles; empty means every panel user
     * @param  string|null  $hint  short note under the figure
     */
    public function stat(string $label, string $icon, Closure $value, ?string $route = null, array $roles = [], int $order = 100, ?string $hint = null): void
    {
        $this->stats[] = compact('label', 'icon', 'value', 'route', 'roles', 'order', 'hint');
    }

    /**
     * @param  Closure(): array<string, int>  $data  ordered label => value pairs
     * @param  string  $type  "bar" (one bar per label) or "line" (a trend over the labels)
     */
    public function chart(string $title, Closure $data, string $type = 'bar', array $roles = [], int $order = 100, ?string $description = null): void
    {
        $this->charts[] = compact('title', 'data', 'type', 'roles', 'order', 'description');
    }

    /**
     * Visible figures in order, with their values resolved.
     */
    public function stats(?Authenticatable $user): array
    {
        return array_map(
            fn (array $stat) => ['value' => (int) ($stat['value'])()] + $stat,
            $this->visible($this->stats, $user)
        );
    }

    /**
     * Visible charts in order, with their data resolved.
     */
    public function charts(?Authenticatable $user): array
    {
        return array_map(
            fn (array $chart) => ['data' => array_map('intval', ($chart['data'])())] + $chart,
            $this->visible($this->charts, $user)
        );
    }

    /**
     * Records created in each of the last $months months, oldest first, keyed
     * by a short month label. With $cumulative the values are running totals
     * that include the records created before the window.
     */
    public static function monthly(Builder $query, int $months = 12, bool $cumulative = false, string $column = 'created_at'): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);

        $counts = array_fill_keys(
            array_map(fn (int $i) => $start->copy()->addMonths($i)->format('Y-m'), range(0, $months - 1)),
            0
        );

        // Grouped in PHP so the query is the same on MySQL and SQLite.
        (clone $query)->where($column, '>=', $start)->pluck($column)->each(function ($date) use (&$counts) {
            $month = Carbon::parse($date)->format('Y-m');
            if (isset($counts[$month])) {
                $counts[$month]++;
            }
        });

        if ($cumulative) {
            $total = (clone $query)->where($column, '<', $start)->count();
            foreach ($counts as $month => $count) {
                $counts[$month] = $total += $count;
            }
        }

        $series = [];
        foreach ($counts as $month => $count) {
            $series[Carbon::createFromFormat('!Y-m', $month)->locale(app()->getLocale())->translatedFormat('M y')] = $count;
        }

        return $series;
    }

    private function visible(array $entries, ?Authenticatable $user): array
    {
        $entries = array_values(array_filter(
            $entries,
            fn (array $entry) => $entry['roles'] === [] || ($user && in_array((int) $user->role, $entry['roles'], true))
        ));

        usort($entries, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return $entries;
    }
}
