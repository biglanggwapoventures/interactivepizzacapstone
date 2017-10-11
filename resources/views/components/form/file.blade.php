<div class="form-group {{ $errors->has($name) ? 'has-error' : '' }}">
    @if($label)
        {{ Form::label($name, $label, ['class' => 'control-label']) }}
    @endif
    {{ Form::file($name, $attributes) }}
    @if($errors->has($name))
        <span class="help-block">{{ $errors->first($name) }}</span>
    @endif
</div>