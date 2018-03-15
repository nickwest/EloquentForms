<?php namespace Nickwest\EloquentForms\Test\unit;

use Faker;

use Nickwest\EloquentForms\Table;
use Illuminate\Support\Collection;

use Nickwest\EloquentForms\Test\TestCase;

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
