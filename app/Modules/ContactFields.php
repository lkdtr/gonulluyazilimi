<?php

namespace App\Modules;

use App\Models\Contact;
use Closure;

/**
 * Catalogue of values that can be shown about a contact outside its profile,
 * e.g. on an ID card. The core and the modules register them, so a module
 * displaying contacts does not need to know the module a value comes from.
 */
class ContactFields
{
    private array $fields = [];

    /** @var array<int, \Closure(): array> producers of fields read at use time (e.g. from the database) */
    private array $resolvers = [];

    private bool $resolved = false;

    /**
     * Fields produced when first needed, e.g. custom fields stored in the
     * database. The closure returns [['key', 'label', value closure, order], ...].
     */
    public function resolver(Closure $resolver): void
    {
        $this->resolvers[] = $resolver;
        $this->resolved = false;
    }

    /**
     * Read the resolvers again, e.g. after a custom field changed.
     */
    public function refresh(): void
    {
        $this->resolved = false;
    }

    private function resolve(): void
    {
        if ($this->resolved) {
            return;
        }
        $this->resolved = true;

        foreach ($this->resolvers as $resolver) {
            foreach ($resolver() as [$key, $label, $value, $order]) {
                $this->fields[$key] = compact('key', 'label', 'value', 'order');
            }
        }
    }

    /**
     * @param  Closure(Contact): (string|null)  $value
     */
    public function register(string $key, string $label, Closure $value, int $order = 100): void
    {
        $this->fields[$key] = compact('key', 'label', 'value', 'order');
    }

    public function has(string $key): bool
    {
        $this->resolve();

        return isset($this->fields[$key]);
    }

    /**
     * Registered fields in order, as key => label.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        $this->resolve();
        $fields = $this->fields;
        uasort($fields, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return array_map(fn (array $field) => $field['label'], $fields);
    }

    public function label(string $key): ?string
    {
        $this->resolve();

        return $this->fields[$key]['label'] ?? null;
    }

    /**
     * The value of a field for the contact; null when unknown or empty.
     */
    public function value(string $key, Contact $contact): ?string
    {
        $this->resolve();

        if (! isset($this->fields[$key])) {
            return null;
        }

        $value = ($this->fields[$key]['value'])($contact);

        return $value === null || $value === '' ? null : (string) $value;
    }
}
