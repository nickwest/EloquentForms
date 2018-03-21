@if($Field->options->hasOptions())
<datalist id="{{ $Field->attributes->id }}">
    @foreach($Field->options->getOptions() as $key => $value)
        <option value="{{ $key }}"{!! $value != '' ? ' label="'.$value.'"' : '' !!}">
    @endforeach
</datalist>
@endif
