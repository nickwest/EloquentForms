@if(count($Field->getOptions()) > 0)
<datalist id="{{ $Field->attributes->id }}">
    @foreach($Field->getOptions() as $key => $value)
        <option value="{{ $key }}"{!! $value != '' ? ' label="'.$value.'"' : '' !!}">
    @endforeach
</datalist>
@endif
