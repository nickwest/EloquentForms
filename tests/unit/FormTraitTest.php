<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;


use Nickwest\EloquentForms\FormTrait;
use Nickwest\EloquentForms\Test\TestCase;


class Sample extends Model
{
    use FormTrait;

    public $table = 'sample';

    public function prepareForm()
    {
        // Default Form Field label postfix
        $this->label_postfix = ':';

        // This is magical and comes from the FormTrait. It generates form field data by looking at the model's table columns
        $this->generateFormData();
    }
}

class FormTraitTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations/'));

        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    public function test_formtrait_form_returns_a_form_object()
    {
        $this->assertInstanceOf(\Nickwest\EloquentForms\Form::class, $this->Model->Form());
    }

    public function test_formtrait_getFormView_returns_a_view()
    {
        $view = $this->Model->getFormView([]);

        $this->assertInstanceOf(View::class, $view);
    }

    // public function test_formtrait_generateFormData_creates_a_form_from_a_models_source_data()
    // {
    //     dump($this->Model->Form()->getFields());

    // }







}
