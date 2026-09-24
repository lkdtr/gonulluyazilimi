{{-- Input of a custom field, named fields[<key>]. $field (CustomField), $value (?string) --}}
@props(['field', 'value' => null, 'bag' => 'default'])

@php($name = 'fields['.$field->key.']')
@php($id = 'field-'.$field->key)
@php($current = old('fields.'.$field->key, $value))
@php($fieldErrors = $errors->getBag($bag))
@php($invalid = $fieldErrors->has('fields.'.$field->key))

<div class="mb-3">
    @if ($field->type === 'checkbox')
        <label class="form-check">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" id="{{ $id }}" class="form-check-input @if ($invalid) is-invalid @endif" name="{{ $name }}" value="1" @checked($current === '1' || $current === true)>
            <span class="form-check-label">{{ $field->label }}</span>
        </label>
    @else
        <label for="{{ $id }}" class="form-label @if ($field->is_required) required @endif">{{ $field->label }}</label>
        @switch($field->type)
            @case('textarea')
                <textarea id="{{ $id }}" name="{{ $name }}" rows="3" maxlength="5000" class="form-control @if ($invalid) is-invalid @endif">{{ $current }}</textarea>
                @break
            @case('select')
                <select id="{{ $id }}" name="{{ $name }}" class="form-select @if ($invalid) is-invalid @endif">
                    <option value="">—</option>
                    @foreach ($field->options ?? [] as $option)
                        <option value="{{ $option }}" @selected($current === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                @break
            @default
                <input id="{{ $id }}" name="{{ $name }}" value="{{ $current }}"
                    type="{{ ['number' => 'number', 'date' => 'date', 'email' => 'email', 'url' => 'url', 'phone' => 'tel'][$field->type] ?? 'text' }}"
                    @if ($field->type === 'number') step="any" @endif
                    class="form-control @if ($invalid) is-invalid @endif">
        @endswitch
    @endif
    @if ($invalid)<div class="invalid-feedback d-block">{{ $fieldErrors->first('fields.'.$field->key) }}</div>@endif
    @if ($field->help)<div class="form-hint">{{ $field->help }}</div>@endif
</div>
