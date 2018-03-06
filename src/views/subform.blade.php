<fieldset{!! (isset($fieldset_id) ? ' id="'.$fieldset_id.'"' : '') !!}{!! (isset($fieldset_class) ? ' class="'.$fieldset_class.'"' : '') !!}>

@if(isset($legend) && $legend != '')
    <legend>{{ $legend }}</legend>
@endif

@php($prev_inline = false)
@foreach($Form->getDisplayFields() as $Field)
    @if($view_only)
        {!! $Field->makeDisplayView($prev_inline) !!}
    @else
        {!! $Field->makeView($prev_inline) !!}
    @endif

    @php($prev_inline = $Field->is_inline ? true : false)
@endforeach

</fieldset>
