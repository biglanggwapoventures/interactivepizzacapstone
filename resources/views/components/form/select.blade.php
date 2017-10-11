<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if($label)
        {{ Form::label($name, $label, ['class' => 'control-label']) }}
    @endif
    {{ Form::select($name, $options, $value, array_merge(['class' => 'form-control'], $attributes)) }}
    @if($errors->has($name))
        <span class="help-block">{{ $errors->first($name) }}</span>
    @endif
</div>