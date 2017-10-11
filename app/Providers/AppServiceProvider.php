<?php

namespace App\Providers;

use Auth;
use Form;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use MyCart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Form::component('bsText', 'components.form.text', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('bsDate', 'components.form.date', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('bsFile', 'components.form.file', ['name', 'label' => null, 'attributes' => []]);
        Form::component('bsPassword', 'components.form.password', ['name', 'label' => null, 'attributes' => []]);
        Form::component('bsTextarea', 'components.form.textarea', ['name', 'label' => null, 'value' => null, 'attributes' => []]);
        Form::component('bsSelect', 'components.form.select', ['name', 'label' => null, 'options' => [], 'value' => [], 'attributes' => []]);
        Form::component('bsCheckbox', 'components.form.checkbox', ['name', 'label', 'value' => null, 'checked' => false, 'attributes' => []]);
        Form::component('bsStatic', 'components.form.static', ['label' => null, 'text' => false]);

        Blade::if ('adminpage', function () {
            return Auth::check() && Auth::user()->isAdmin() && request()->is('admin/*');
        });

        Blade::if ('shoppage', function () {
            return !request()->is('admin/*');
        });

        Blade::directive('print_r', function ($data) {
            return "<?php echo \"<pre>\"?><?php print_r({$data})?><?php echo \"</pre>\"?>";
        });

        Blade::directive('cartcount', function () {
            $count = MyCart::getItemCount();
            return $count ? "<span class=\"badge\">{$count}</span>" : '';
        });

        Blade::directive('csrftoken', function () {
            return "\"" . csrf_token() . "\"";
        });

        Blade::directive('route', function ($route) {
            $url = route($route);
            return "\"{$route}\"";
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
