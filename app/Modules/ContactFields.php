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

    /**
     * @param  Closure(Contact): (string|null)  $value
     */
    public function register(string $key, string $label, Closure $value, int $order = 100): void
    {
        $this->fields[$key] = compact('key', 'label', 'value', 'order');
    }

    public function has(string $key): bool
    {
        return isset($this->fields[$key]);
    }

    /**
     * Registered fields in order, as key => label.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        $fields = $this->fields;
        uasort($fields, fn (array $a, array $b) => $a['order'] <=> $b['order']);

        return array_map(fn (array $field) => $field['label'], $fields);
    }

    public function label(string $key): ?string
    {
        return $this->fields[$key]['label'] ?? null;
    }

    /**
     * The value of a field for the contact; null when unknown or empty.
     */
    public function value(string $key, Contact $contact): ?string
    {
        if (! isset($this->fields[$key])) {
            return null;
        }

        $value = ($this->fields[$key]['value'])($contact);

        return $value === null || $value === '' ? null : (string) $value;
    }
}
