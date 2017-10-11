<div class="checkbox form-group {{ $errors->has($name) ? 'has-error' : '' }}">
  	<label>
  		 {{ Form::checkbox($name, $value, $checked, array_merge([], $attributes)) }}
  		 {{ $label }}
  	</label>
   
    @if($errors->has($name))
        <span class="help-block">{{ $errors->first($name) }}</span>
    @endif
</div>