{{-- Don't include this, use $Field->makeOptionView() instead --}}
@if(!$view_only || $Field->attributes->checked)
<div class="{{ $Field->options->wrapper_class }}">
    @if(!$view_only)
        <input {!! $Field->attributes !!}>
    @endif
    <label class="{{ $Field->options->label_class }}" for="{{ $Field->attributes->id }}">
        {{ $Field->options->$key }}
    </label>
</div>
@endif
