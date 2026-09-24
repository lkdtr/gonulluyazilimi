{{--
    Checkbox accepting an agreement in force; renders nothing while the
    agreement has no published version. The page defines openModal(url).
--}}
@props(['key', 'name' => 'agreement'])

@if ($version = app(\App\Support\Agreements::class)->current($key))
    <div class="row">
        <label class="col-md-8 offset-md-4 mb-3" for="{{ $name }}-{{ $key }}">
            <input name="{{ $name }}" id="{{ $name }}-{{ $key }}" value="true" type="checkbox" required>
            &nbsp; <a href="javascript:openModal('{{ route('agreements.show', $key, false) }}')">{{ $version->agreement->title }}</a> koşullarını kabul ediyorum
        </label>
    </div>
@endif
