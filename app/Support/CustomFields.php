<?php

namespace App\Support;

use App\Models\Contact;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

/**
 * Custom fields of contacts: the groups they are shown in (the core's and
 * those modules register), validation, reading and saving values.
 */
class CustomFields
{
    private array $groups = [
        'personal' => ['label' => 'Kişisel', 'order' => 10],
        'contact' => ['label' => 'İletişim', 'order' => 20],
        'work' => ['label' => 'İşyeri', 'order' => 30],
        'other' => ['label' => 'Diğer', 'order' => 90],
    ];

    public function group(string $key, string $label, int $order = 50): void
    {
        $this->groups[$key] = compact('label', 'order');
    }

    /**
     * @return array<string, string> group key => label, in order
     */
    public function groups(): array
    {
        $groups = $this->groups;
        uasort($groups, fn ($a, $b) => $a['order'] <=> $b['order']);

        return array_map(fn ($group) => $group['label'], $groups);
    }

    /**
     * Active fields that apply to the contact, in group and field order.
     * $member limits them to what the person may see.
     *
     * @return Collection<int, CustomField>
     */
    public function fieldsFor(Contact $contact, bool $member = false): Collection
    {
        $order = array_flip(array_keys($this->groups()));

        return CustomField::active()->for($contact)
            ->when($member, fn ($query) => $query->whereIn('member_access', ['visible', 'editable']))
            ->get()
            ->sortBy(fn (CustomField $field) => [($order[$field->group] ?? 999), $field->sort, $field->id])
            ->values();
    }

    /**
     * @return array<int, string> field id => stored value
     */
    public function values(Contact $contact): array
    {
        return CustomFieldValue::where('contact_id', $contact->id)->pluck('value', 'custom_field_id')->all();
    }

    /**
     * Validation rules for "fields.<key>" inputs.
     *
     * @param  iterable<CustomField>  $fields
     */
    public function rules(iterable $fields): array
    {
        $rules = [];
        foreach ($fields as $field) {
            $base = $field->is_required && $field->type !== 'checkbox' ? ['required'] : ['nullable'];
            $rules['fields.'.$field->key] = array_merge($base, match ($field->type) {
                'number' => ['numeric'],
                'date' => ['date'],
                'email' => ['email', 'max:150'],
                'url' => ['url:http,https', 'max:255'],
                'phone' => ['string', 'max:30', 'regex:/^[0-9+() .-]+$/'],
                'select' => [Rule::in($field->options ?? [])],
                'checkbox' => ['boolean'],
                'textarea' => ['string', 'max:5000'],
                default => ['string', 'max:255'],
            });
        }

        return $rules;
    }

    /**
     * Attribute names for validation messages.
     *
     * @param  iterable<CustomField>  $fields
     */
    public function attributes(iterable $fields): array
    {
        $names = [];
        foreach ($fields as $field) {
            $names['fields.'.$field->key] = $field->label;
        }

        return $names;
    }

    /**
     * Store the validated values of the given fields; empty removes a value.
     *
     * @param  iterable<CustomField>  $fields
     * @param  array<string, mixed>  $input  key => value (validated)
     */
    public function save(Contact $contact, iterable $fields, array $input): void
    {
        foreach ($fields as $field) {
            $value = $input[$field->key] ?? null;
            $value = match ($field->type) {
                'checkbox' => ! empty($value) ? '1' : null,
                'date' => $value ? Carbon::parse($value)->toDateString() : null,
                default => is_string($value) ? trim($value) : ($value === null ? null : (string) $value),
            };

            $existing = CustomFieldValue::where('contact_id', $contact->id)->where('custom_field_id', $field->id)->first();

            if ($value === null || $value === '') {
                $existing?->delete();
            } elseif ($existing) {
                $existing->update(['value' => $value]);
            } else {
                CustomFieldValue::create(['contact_id' => $contact->id, 'custom_field_id' => $field->id, 'value' => $value]);
            }
        }
    }
}
