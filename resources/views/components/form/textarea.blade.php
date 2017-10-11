<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if($label)
        {{ Form::label($name, $label, ['class' => 'control-label']) }}
    @endif
    {{ Form::textarea($name, $value, array_merge(['class' => 'form-control', 'rows' => 3], $attributes)) }}
    @if($errors->has($name))
        <span class="help-block">{{ $errors->first($name) }}</span>
    @endif
</div>