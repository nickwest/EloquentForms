<?php namespace Nickwest\EloquentForms\Test\unit;

use Config;

use Illuminate\View\View;
use Illuminate\Support\Facades\Artisan;

use Nickwest\EloquentForms\Test\Sample;
use Nickwest\EloquentForms\Test\TestCase;

class FormTraitTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../database/migrations/'));

        // Sample is a class declared in the bottom of this file
        // It is only used in these tests
        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    // This test runs with SQLite as the Driver.
    // It uses default Doctrine/Dbal to get the column info to generate the form fields
    public function test_formtrait_will_generate_a_form_from_a_db_structure()
    {
        $array = $this->Model->getColumnsArray();

        $this->assertEquals($this->expectedDBStructure(true), $array);
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

    public function test_formtrait_getFieldView_returns_a_view()
    {
        foreach($this->Model->Form()->getFields() as $Field){
            $this->assertInstanceOf(View::class, $this->Model->getFieldView($Field->attributes->name));
        }
    }

    public function test_formtrait_getFieldDisplayView_returns_a_view()
    {
        foreach($this->Model->Form()->getFields() as $Field){
            $this->assertInstanceOf(View::class, $this->Model->getFieldDisplayView($Field->attributes->name));
        }
    }

    public function test_formtrait_setAllFormValues_sets_values_to_the_form_from_the_model()
    {
        $post_data = $this->getSimulatedPostValues();
        foreach($post_data as $field => $value){
            if(is_array($value)){
                $value = implode('|', $value);
            }
            $this->Model->{$field} = $value;
        }

        // Set the form values from the Model's values
        $this->Model->setAllFormValues();

        // getFieldValues is tested in FormTests
        $values = $this->Model->Form()->getFieldValues();


        // Unset ID and timestamps since they're not in the post data used to generate the model
        unset($values['id']);
        unset($values['created_at']);
        unset($values['updated_at']);
        unset($values['volumes']); // Datalists arenn't real fields either

        // The form should now have equal values to what's in post_data,
        $this->assertEquals($post_data, $values);
    }

    public function test_formtrait_setPostValues_sets_post_data_to_the_form_and_the_model()
    {
        $post_data = $this->getSimulatedPostValues();
        // Id is not fillable
        $post_data['id'] = 100;

        // Try to set $post_data with ID
        $this->Model->setPostValues($post_data);

        // remove id from post_data for comparison
        $post_data['id'];

        // If ID got set, these be equal and fail
        $this->assertNotEquals(100, $this->Model->id);
        $this->assertNotEquals(100, $this->Model->Form()->id->attributes->value);
    }

    public function test_formtrait_setPostValues_obeys_model_fillable_settings()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        foreach($post_data as $field => $value) {
            $this->assertEquals($value, $this->Model->Form()->{$field}->attributes->value);
            // on the model arrays are flattened
            if(is_array($value)){
                $value = implode('|', $value);
            }
            $this->assertEquals($value, $this->Model->{$field});
        }
    }

    public function test_fortrait_setPostValues_can_handle_multi_key_fields()
    {
        $post_data = $this->getSimulatedPostValues();
        $post_data['fruits_liked'] = ['Banana', 'Apple', 'Peach'];

        $this->Model->setPostValues($post_data);

        // The model should have flattened values
        $expected = 'Banana|Apple|Peach';

        $this->assertEquals($post_data['fruits_liked'], $this->Model->Form()->fruits_liked->attributes->value);
        $this->assertEquals($expected, $this->Model->fruits_liked);
    }

    public function test_formtrait_setPostValues_will_set_boolean_checkboxes_to_false_when_null()
    {
        $post_data = $this->getSimulatedPostValues();

        // Single checkbox will not be in the post
        unset($post_data['is_hidden']);

        $this->Model->setPostValues($post_data);

        $this->assertSame(false, $this->Model->is_hidden);
        $this->assertSame(false, $this->Model->Form()->is_hidden->attributes->value);
    }

    public function test_formtrait_models_force_validation_on_save_by_default()
    {
        $this->assertAttributeEquals(true, 'validate_on_save', $this->Model);
    }

    public function test_formtrait_adds_extra_validation_and_that_works()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($this->getSimulatedPostValues());

        // $this->Model->isFormValid();
        // foreach($this->Model->Form()->getFields() as $Field){
        //     if($Field->error_message != ''){
        //         dd($Field->attributes->name.' '.$Field->error_message);
        //     }
        // }

        // Simple case, completely valid
        $this->assertTrue($this->Model->isFormValid());
    }

    public function test_formtrait_models_validate_on_save_will_run_validation_before_saving()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        // Make it invalid
        $this->Model->email = 'NotEmail';

        // Make sure it's not valid
        $this->assertFalse($this->Model->isFormValid());

        // Trying to save should fail and return false
        $this->assertFalse($this->Model->save());
    }

    public function test_formtrait_models_can_block_validation_on_save()
    {
        $post_data = $this->getSimulatedPostValues();

        $this->Model->setPostValues($post_data);

        // Make it invalid
        $this->Model->email = 'not_an_email1234';

        $this->Model->validate_on_save = false;

        $this->assertTrue($this->Model->save());
    }

    public function test_formtrait_save_works_when_form_not_prepared()
    {
        $Sample = new Sample;

        // Set the two not-nullable fields
        $Sample->first_name = 'Tester';
        $Sample->last_name = 'McTesting';

        // Save without preparing the form
        $this->assertTrue($Sample->save());
    }

    public function test_formtrait_save_works_when_form_not_prepared_but_form_validation_doesnt_work()
    {
        $Sample = new Sample;

        // Set the two not-nullable fields
        $Sample->first_name = 'Tester';
        $Sample->last_name = 'McTesting';
        $Sample->email = 'not_an_email_address';

        // Save without preparing the form
        $this->assertTrue($Sample->save());
    }

    public function test_formtrait_generateFormFromJson_works()
    {
        // This is testing in FormTests
        $json = $this->Model->Form()->toJson();

        $newModel = new Sample();
        $newModel->generateFormFromJson($json);

        // Their forms should be equal
        $this->assertEquals($this->Model->Form(), $newModel->Form());
    }

}
