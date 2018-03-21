{{-- Don't include this, use $Field->makeOptionView() instead --}}
@if(!$view_only || $Field->attributes->checked)
<div class="{{ $Field->options->wrapper_class }}">
    <label class="{{ $Field->options->label_class }}" for="{{ $Field->attributes->id }}">
        @if($view_only)
            {{ $Field->options->getOption($key) }}
        @else
            <input {!! $Field->attributes !!}>
            {{ $Field->options->getOption($key) }}
        @endif
    </label>
</div>
@endif
