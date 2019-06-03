<?php namespace Nickwest\EloquentForms\Test\unit\CustomFields;

use Faker;

use Illuminate\View\View;

use Nickwest\EloquentForms\Attributes;
use Nickwest\EloquentForms\Test\Sample;
use Nickwest\EloquentForms\Test\TestCase;

class DaysOfWeekFieldTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(realpath(__DIR__.'/../../database/migrations/'));

        // Sample is a class declared in the bottom of this file
        // It is only used in these tests
        $this->Model = new Sample();
        $this->Model->prepareForm();
    }

    public function test_makeView_returns_a_view()
    {
        $Field = $this->Model->Form()->getField('favorite_days');

        $view = $Field->makeView();

        $this->assertInstanceOf(View::class, $view);
    }

    public function test_value_sets_with_set_post_values()
    {
        $post_values = $this->getSimulatedPostValues();
        $this->Model->setPostValues($post_values);

        $this->assertEquals(['M', 'W', 'F'], $this->Model->Form()->favorite_days->attributes->value);

        $this->assertEquals('M|W|F', $this->Model->favorite_days);
    }

    public function test_field_saves()
    {
        $post_values = $this->getSimulatedPostValues();
        $this->Model->setPostValues($post_values);

        $this->assertNull($this->Model->getKey());

        $this->Model->save();

        $this->assertNotNull($this->Model->getKey());
    }

    public function test_value_sets_with_set_all_form_values()
    {
        $post_values = $this->getSimulatedPostValues();
        $this->Model->setPostValues($post_values);

        $this->assertNull($this->Model->getKey());

        $this->Model->save();


        $newModel = Sample::find($this->Model->getKey());
        $this->assertEquals('M|W|F', $newModel->favorite_days);

        $newModel->prepareForm();
        $newModel->setAllFormValues();
        $this->assertEquals(['M', 'W', 'F'], $newModel->Form()->favorite_days->attributes->value);
    }
}
