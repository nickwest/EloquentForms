@if($Field->label != '')
    <label class="{{ $Field->label_class }}" for="{{ $Field->attributes->id }}">
        {{ $Field->label }}{{ $Field->label_suffix ? $Field->label_suffix : '' }}@if(isset($Field->attributes->required)) <em>*</em> @endif
    </label>
@endif
