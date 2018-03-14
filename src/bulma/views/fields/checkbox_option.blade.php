{{-- Don't include this, use $Field->makeOptionView() instead --}}
@if(!$view_only || $Field->attributes->checked)
<div class="{{ $Field->option_wrapper_class }}">
    <label class="{{ $Field->option_label_class }}" for="{{ $Field->attributes->id }}">
        @if($view_only)
            {{ $Field->getOption($key) }}
        @else
            <input {!! $Field->attributes !!}>
            {{ $Field->getOption($key) }}
        @endif
    </label>
</div>
@endif
