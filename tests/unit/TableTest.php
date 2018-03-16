<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;
use Route;

use Illuminate\Support\Collection;

use Nickwest\EloquentForms\Table;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Exceptions\InvalidRouteException;
use Nickwest\EloquentForms\Exceptions\InvalidFieldException;

class TableTest extends TestCase
{
    /**
     * @var array
     */
    protected $display_fields = ['name' => 'name', 'birthday' => 'birthday', 'email' => 'email'];

    /**
     * @var Illuminate\Support\Collection
     */
    protected $Collection = null;

    /**
     * @var Nickwest\EloquentForms\Table
     */
    protected $Table = null;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['router']->get('sample/data', ['as' => 'sample', 'uses' => function () {
            return 'This is a sample route';
        }]);
    }


    public function setUp()
    {
        parent::setUp();

        $this->Table = new Table;
        $this->Faker = Faker\Factory::create();

        $this->Collection = $this->makeData();
        $this->Table->setData($this->Collection);
    }

    public function test_table_has_some_stuff_when_created()
    {
        $this->assertAttributeInstanceOf(\Nickwest\EloquentForms\Attributes::class, 'attributes', $this->Table);
        $this->assertAttributeInstanceOf(\Nickwest\EloquentForms\Theme::class, 'Theme', $this->Table);
    }

    public function test_table_setData_will_assign_data_to_object()
    {
        $this->assertattributeEquals($this->Collection, 'Collection', $this->Table);
    }

    public function test_table_setDisplayFields_sets_display_fields()
    {
        // The can be set with non-keyed array
        $this->Table->setDisplayFields(['name', 'birthday', 'email']);

        $this->assertAttributeEquals($this->display_fields, 'display_fields', $this->Table);
    }

    public function test_table_getDisplayFields_retrieves_display_fields()
    {
        // The can be set with non-keyed array
        $this->Table->setDisplayFields(['name', 'birthday', 'email']);

        $this->assertEquals($this->display_fields, $this->Table->getDisplayFields());
    }

    public function test_table_setTheme_sets_the_theme_on_the_form()
    {
        // Verify it starts with the Default theme set
        $this->assertInstanceOf(\Nickwest\EloquentForms\DefaultTheme::class, $this->Table->getTheme());

        $myTheme = new \Nickwest\EloquentForms\Themes\bulma\Theme();
        $this->Table->setTheme($myTheme);

        $this->assertInstanceOf(\Nickwest\EloquentForms\Themes\bulma\Theme::class, $this->Table->getTheme());
    }

    public function test_table_setLabels_sets_labels_to_table()
    {
        $this->Table->setDisplayFields($this->display_fields);
        $labels = [
            'name' => 'Name',
            'email' => 'E-mail',
            'birthday' => 'Birthday',
        ];

        $this->Table->setLabels($labels);

        $this->assertAttributeEquals($labels, 'labels', $this->Table);
    }

    public function test_table_setLabels_throws_exception_when_invalid_field_passed()
    {
        $this->Table->setDisplayFields($this->display_fields);
        $labels = [
            'name' => 'Name',
            'email' => 'E-mail',
            'birthday' => 'Birthday',
            'phone_number' => 'Digits',
        ];
        $this->expectException(InvalidFieldException::class);
        $this->Table->setLabels($labels);

    }

    public function test_table_setLabels_gets_a_labels()
    {
        $this->Table->setDisplayFields($this->display_fields);
        $labels = [
            'name' => 'Name',
            'email' => 'E-mail',
            'birthday' => 'Birthday',
        ];

        $this->Table->setLabels($labels);

        foreach($this->display_fields as $field_name){
            $this->assertEquals($labels[$field_name], $this->Table->getLabel($field_name));
        }
    }

    public function test_table_addFieldReplacement_adds_a_field_replacement_pattern()
    {
        $this->Table->addFieldReplacement('birthday', '<strong>{birthday}</strong>');

        $this->assertAttributeEquals(['birthday' => '<strong>{birthday}</strong>'], 'field_replacements', $this->Table);
    }

    public function test_table_hasFieldReplacement_confirms_existence_of_replacement_pattern()
    {
        $this->Table->addFieldReplacement('birthday', '<strong>{birthday}</strong>');

        $this->assertTrue($this->Table->hasFieldReplacement('birthday'));
        $this->assertFalse($this->Table->hasFieldReplacement('email'));
    }

    public function test_table_getFieldReplacement_returns_the_replaced_value()
    {
        $this->Table->addFieldReplacement('birthday', '<strong>{birthday}</strong>');

        $item = $this->Collection->pop();
        $this->assertEquals('<strong>'.$item['birthday'].'</strong>', $this->Table->getFieldReplacement('birthday', $item));
    }

    public function test_table_getFieldReplacement_replaces_other_field_tokens_too()
    {
        $this->Table->addFieldReplacement('birthday', '<strong>{email} {birthday}</strong>');

        $item = $this->Collection->pop();
        $this->assertEquals('<strong>'.$item['email'].' '.$item['birthday'].'</strong>', $this->Table->getFieldReplacement('birthday', $item));
    }

    public function test_table_setLinkingPattern_sets_a_linking_pattern_for_a_field()
    {
        $this->Table->setLinkingPatternByRoute('name', 'sample');

        $this->assertAttributeEquals(['name' => '<a href="/sample/data">{name}</a>'], 'field_replacements', $this->Table);
    }

    public function test_table_setLinkingPattern_throws_exception_on_invalid_route()
    {
        $this->expectException(InvalidRouteException::class);
        $this->Table->setLinkingPatternByRoute('name', 'no.route');
    }

    public function test_table_makeView_returns_a_view()
    {
        $this->Table->setDisplayFields($this->display_fields);
        $labels = [
            'name' => 'Name',
            'email' => 'E-mail',
            'birthday' => 'Birthday',
        ];

        $this->Table->setLabels($labels);

        $View = $this->Table->makeView();

        $this->assertInstanceOf(\Illuminate\View\View::class, $View);
    }

    public function test_table_exportToExcel_created_an_excel_file()
    {

    }





    /**
     *  Make a test collection full of data
     *
     * @return Illuminate\Support\Collection
     */
    protected function makeData()
    {
        $Collection = new Collection();
        for($i = 0; $i < 35; $i++){
            $Collection->push([
                'name' => $this->Faker->name,
                'email' => $this->Faker->email,
                'birthday' => $this->Faker->date,
                'phone_number' => $this->Faker->phoneNumber,
                'bio' => $this->Faker->text,
            ]);
        }

        return $Collection;
    }

}
