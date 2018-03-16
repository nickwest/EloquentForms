<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Attributes;
use Nickwest\EloquentForms\Test\TestCase;

class AttributesTest extends TestCase
{
    public function setUp(){
        parent::setUp();
    }

    public function test_attributes_set_magic_method_sets_an_attribute()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->method = 'POST';
        $Attributes->{'v-for'} = 'something for JS stuff';

        $this->assertTrue(isset($Attributes->disabled));
        $this->assertEquals('POST', $Attributes->method);
        $this->assertEquals('something for JS stuff', $Attributes->{'v-for'});
    }

    public function test_attributes_get_magic_method_gets_an_attribute()
    {
        $Attributes = new Attributes();
        $Attributes->method = 'POST';

        $this->assertEquals('POST', $Attributes->method);
    }

    public function test_attributes_isset_magic_method_checks_if_an_attribute_is_set()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->id = 'my id';

        $this->assertTrue(isset($Attributes->disabled));
        $this->assertTrue(isset($Attributes->id));
    }

    public function test_attributes_unset_magic_method_unsets_an_attribute()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->id = 'my id';

        unset($Attributes->disabled);
        unset($Attributes->id);

        $this->assertFalse(isset($Attributes->disabled));
        $this->assertFalse(isset($Attributes->id));
    }

    public function test_attributes_getting_an_invalid_attribute_returns_null()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->id = 'my id';

        $this->assertNull($Attributes->blahblah);
    }

    public function test_attributes_toString_magic_method_creates_attribute_string()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->id = 'my id';
        $Attributes->method = 'POST';
        $Attributes->{'v-for'} = 'something for JS stuff';

        $this->assertEquals('disabled id="input-my-id" method="POST" v-for="something for JS stuff"', (string)$Attributes);
    }

    public function test_attributes_getRawID_returns_unmodified_id()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->id = 'my_id';
        $Attributes->id_prefix = 'secret-';

        $this->assertEquals('my_id', $Attributes->getRawID());
        $this->assertEquals('secret-my_id', $Attributes->id);
    }

    public function test_attributes_id_prefix_can_be_changed()
    {
        $Attributes = new Attributes();

        // Set some attributes
        $Attributes->disabled = null;
        $Attributes->id = 'my id';
        $Attributes->id_prefix = 'secret-';
        $Attributes->method = 'POST';
        $Attributes->{'v-for'} = 'something for JS stuff';

        $this->assertEquals('disabled id="secret-my-id" method="POST" v-for="something for JS stuff"', (string)$Attributes);
    }

    public function test_attributes_id_suffix_can_be_set_and_shows_up()
    {
        $Attributes = new Attributes();

        $Attributes->id = 'my_id';
        $Attributes->id_suffix = '-42';

        $this->assertEquals('id="input-my_id-42"', (string)$Attributes);
    }

    public function test_attributes_get_method_for_id_obeys_prefix_and_suffix()
    {
        $Attributes = new Attributes();

        $Attributes->id = 'my_id';
        $Attributes->id_suffix = '-42';
        $Attributes->id_prefix = 'secret-';

        $this->assertEquals('secret-my_id-42', $Attributes->id);
    }

    public function test_attributes_addClass_adds_a_class_to_the_class_attribute()
    {
        $Attributes = new Attributes();

        $Attributes->addClass('red');

        $this->assertEquals('red', $Attributes->class);
        $this->assertEquals('class="red"', (string)$Attributes);
    }

    public function test_attributes_addClasses_adds_multiple_classes_to_the_class_attribute()
    {
        $Attributes = new Attributes();

        $Attributes->addClasses(['red', 'bold', 'big']);

        $expected = ['red' => 'red', 'bold' => 'bold', 'big' => 'big'];

        $this->assertAttributeEquals($expected, 'classes', $Attributes);
        $this->assertEquals('red bold big', $Attributes->class);
        $this->assertEquals('class="red bold big"', (string)$Attributes);

    }

    public function test_attributes_adding_multiple_classes_produces_a_valid_class_string()
    {
        $Attributes = new Attributes();

        $Attributes->addClass('red');
        $Attributes->addClass('big');
        $Attributes->addClass('bold');

        $this->assertEquals('red big bold', $Attributes->class);
        $this->assertEquals('class="red big bold"', (string)$Attributes);
    }

    public function test_attributes_removeClass_removes_a_class_from_the_class_attribute()
    {
        $Attributes = new Attributes();

        $Attributes->addClass('red');
        $Attributes->addClass('big');
        $Attributes->addClass('bold');

        $Attributes->removeClass('big');
        $this->assertEquals('red bold', $Attributes->class);
    }

    public function test_attributes_hasClass_returns_if_a_class_name_exists()
    {
        $Attributes = new Attributes();

        $Attributes->addClass('red');
        $Attributes->addClass('big');
        $Attributes->addClass('bold');

        $this->assertTrue($Attributes->hasClass('red'));
        $this->assertTrue($Attributes->hasClass('bold'));
        $this->assertFalse($Attributes->hasClass('green'));
    }

    public function test_attributes_multi_key_sets_name_attribute_accordingly()
    {
        $Attributes = new Attributes();

        $Attributes->name = 'people';
        $Attributes->multi_key = true;

        $this->assertEquals('name="people[]"', (string)$Attributes);

        $Attributes->multi_key = 1;
        $this->assertEquals('name="people[1]"', (string)$Attributes);
    }

    public function test_attributes_toJson_produces_a_json_string()
    {
        $Attributes = new Attributes();

        $Attributes->disabled = null;
        $Attributes->id = 'my id';
        $Attributes->method = 'POST';
        $Attributes->{'v-for'} = 'something for JS stuff';

        $Attributes->addClass('red');
        $Attributes->addClass('big');
        $Attributes->addClass('bold');

        $Attributes->multi_key = 4;

        $json = $Attributes->toJson();

        // The json is valid
        $this->assertJson($json);
    }

    public function test_attributes_toJson_produces_json_that_represents_object()
    {
        $Attributes = new Attributes();

        $Attributes->disabled = null;
        $Attributes->id = 'my id';
        $Attributes->method = 'POST';
        $Attributes->{'v-for'} = 'something for JS stuff';

        $Attributes->addClass('red');
        $Attributes->addClass('big');
        $Attributes->addClass('bold');

        $Attributes->multi_key = 4;

        $json = $Attributes->toJson();

        $NewAttributes = new Attributes();

        $NewAttributes->fromJson($json);

        $this->assertEquals($Attributes, $NewAttributes);

    }



}
