<?php

use Nickwest\FormMaker\Form;
use Nickwest\FormMaker\Field;

class FormTest extends PHPUnit_Framework_TestCase
{
	protected $test_fields;

	function setUp(){
		$this->Form = new Form;

		$this->test_fields = array('First Field', 'WeirdCharacters!@#$%^&*()_+\'\\', 'Third field', 'My Special Field');
		$this->Form->addFields($this->test_fields);
	}

	/** @test */
	function a_form_has_days_of_week()
	{
		$this->setUp();

		// What days of week should look like
		$days_of_week = array('M' => 'Mon', 'T' => 'Tue', 'W' => 'Wed', 'R' => 'Thu', 'F' => 'Fri', 'S' => 'Sat', 'U' => 'Sun');
		$this->assertEquals($days_of_week, $this->Form->getDaysOfWeekValues());
	}

	/** @test */
	function a_form_can_allow_delete()
	{
		$this->setUp();

		$this->Form->allow_delete = true;

		$view = $this->Form->makeView(array());
	}

	/** @test */
	function a_form_will_not_let_you_set_invalid_properties()
	{
		$this->setUp();

		$this->expectException(Exception::class);
		$this->Form->this_property_does_not_exist = true;
	}

	/** @test */
	function a_form_can_have_fields_added()
	{
		$this->setUp();

		$this->Form->addFields(array('new', 'new2'));

		$this->assertInternalType('array', $this->Form->getFields());
		$this->assertCount(6, $this->Form->getFields());
		$this->assertContainsOnly('Nickwest\FormMaker\Field', $this->Form->getFields());
	}

	/** @test */
	function a_form_can_have_fields_removed()
	{
		$this->setUp();

		$this->Form->removeFields($this->test_fields);

		$this->assertInternalType('array', $this->Form->getFields());
		$this->assertCount(0, $this->Form->getFields());
	}

	/** @test */
	function a_form_can_have_one_field_added()
	{
		$this->setUp();

		$field_name = 'My Latest Test Fields';
		$this->Form->addField($field_name);

		$this->assertInstanceOf('Nickwest\FormMaker\Field', $this->Form->$field_name);
		$this->assertInternalType('array', $this->Form->getFields());
		$this->assertCount(count($this->test_fields) + 1, $this->Form->getFields());
		$this->assertContainsOnly('Nickwest\FormMaker\Field', $this->Form->getFields());
	}

	/** @test */
	function a_form_can_have_one_field_removed()
	{
		$this->setUp();

		$this->Form->removeField('First Field');

		$this->assertCount(3, $this->Form->getFields());
		$this->assertContainsOnly('Nickwest\FormMaker\Field', $this->Form->getFields());
		$this->assertSame(null, $this->Form->{'First Field'});
	}


}

?>
