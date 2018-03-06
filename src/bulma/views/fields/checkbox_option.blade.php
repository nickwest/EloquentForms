{{-- Don't include this, use $Field->makeOptionView() instead --}}
@if(!$view_only || $Field->attributes->checked)
<div class="{{ $Field->option_wrapper_class }}">
    <label class="{{ $Field->option_label_class }}" for="{{ $Field->attributes->id }}">
        @if($view_only)
            {{ $Field->options[$key] }}
        @else
            <input {!! $Field->attributes !!}>
            {{ $Field->options[$key] }}
        @endif
    </label>
</div>
@endif
