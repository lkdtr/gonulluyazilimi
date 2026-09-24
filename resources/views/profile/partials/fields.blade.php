{{--
    Custom fields on the profile page. $fields (visible to the person), $values
    (field id => value), $groups, $editable: the person's own profile.
--}}
@if ($fields->isNotEmpty())
    <div class="card mt-3" id="fields">
        <div class="card-header"><h3 class="card-title">Ek bilgiler</h3></div>
        <div class="card-body">
            @if (session('fields-status'))
                <div class="alert alert-success" role="alert">{{ session('fields-status') }}</div>
            @endif

            @php($editableFields = $editable ? $fields->where('member_access', 'editable') : collect())
            @php($readOnly = $fields->diff($editableFields))

            @if ($readOnly->isNotEmpty())
                <dl class="row">
                    @foreach ($readOnly as $field)
                        <dt class="col-md-4">{{ $field->label }}</dt>
                        <dd class="col-md-8">{{ $field->display($values[$field->id] ?? null) ?? '—' }}</dd>
                    @endforeach
                </dl>
            @endif

            @if ($editableFields->isNotEmpty())
                <form method="POST" action="{{ route('my-fields.update') }}">
                    @csrf @method('PUT')
                    <div class="row">
                        @foreach ($editableFields as $field)
                            <div class="col-md-6"><x-custom-field-input :field="$field" :value="$values[$field->id] ?? null" bag="memberFields" /></div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            @endif
        </div>
    </div>
@endif
