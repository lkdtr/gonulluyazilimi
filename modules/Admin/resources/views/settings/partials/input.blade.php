<div class="mb-3">
    <label for="{{ $key }}" class="form-label">{{ $label }}</label>
    <input id="{{ $key }}" name="{{ $key }}" type="{{ $type }}" value="{{ $value }}" class="form-control @if ($errors->has($key)) is-invalid @endif" @if ($placeholder) placeholder="{{ $placeholder }}" @endif>
    @if ($errors->has($key))<div class="invalid-feedback">{{ $errors->first($key) }}</div>@endif
    @if ($hint)<div class="form-hint">{{ $hint }}</div>@endif
</div>
