<?php namespace Nickwest\EloquentForms\Test\view\defaults;

use Faker;

use Illuminate\Support\Collection;

use Sunra\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Table;
use Nickwest\EloquentForms\Test\TestCase;

class tableButtonsTest extends TestCase
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

        $this->Collection = $this->makeData(5);
        $this->Table->setData($this->Collection);
        $this->Table->setDisplayFields($this->display_fields);
    }

    public function test_table_view_has_a_table()
    {
        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $table = current($dom->find('table'));

        $this->assertNotFalse($table);
    }

    public function test_table_can_have_classes()
    {
        $this->Table->attributes->addClasses(['red', 'big']);

        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $table = current($dom->find('table'));

        $this->assertEquals('red big', $table->class);
    }

    public function test_table_can_have_any_attributes()
    {
        $this->Table->attributes->{'v-hide:hidden'} = null;
        $this->Table->attributes->align = 'left';
        $this->Table->attributes->id = 'my-table';

        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $table = current($dom->find('table'));

        $this->assertTrue($table->{'v-hide:hidden'});
        $this->assertEquals('left', $table->align);
        $this->assertEquals('my-table', $table->id);
    }





    /**
     *  Make a test collection full of data
     *
     * @return Illuminate\Support\Collection
     */
    protected function makeData(int $count)
    {
        $Collection = new Collection();
        for($i = 0; $i < $count; $i++){
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
