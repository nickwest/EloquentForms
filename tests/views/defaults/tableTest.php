<?php namespace Nickwest\EloquentForms\test\view\defaults;

use Faker;

use Illuminate\Support\Collection;

use KubAT\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Table;
use Nickwest\EloquentForms\test\TestCase;

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

    public function setUp(): void
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

    public function test_table_has_necessary_markup()
    {
        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $table = current($dom->find('table'));

        $this->assertNotFalse($table->find('thead'));
        $this->assertNotFalse($table->find('tbody'));
    }

    public function test_table_has_headings_as_set()
    {
        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $ths = $dom->find('thead tr th');

        foreach($ths as $th){
            $this->assertEquals(e($this->Table->getLabel($th->{'data-field'})), $th->plaintext);
        }
    }

    public function test_table_has_cells_as_set()
    {
        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $trs = $dom->find('tr');

        // Remove the first one since that's the headings
        array_shift($trs);

        foreach($trs as $tr){
            $data = $this->Collection->shift();
            foreach($tr->find('td') as $td){
                $this->assertEquals(e($data[$td->{'data-field'}]), trim($td->innertext));
            }
        }
    }

    public function test_table_can_have_custom_headings()
    {
        $labels = ['name' => 'Full name', 'email' => 'E-mail'];
        $this->Table->setLabels($labels);

        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $ths = $dom->find('thead tr th');

        foreach($ths as $th){
            if(isset($labels[$th->{'data-field'}])){
                $this->assertEquals(e($labels[$th->{'data-field'}]), trim($th->innertext));
            }
        }
    }

    public function test_table_can_have_replacement_pattern()
    {
        // Inject something nasty
        $Collection = new Collection();
        $item = $this->Collection->pop();
        $item['name'] = '<script>alert("test")</script>';
        $item['birthday'] = '<script>alert("Happy Birthday!")</script>';
        $Collection->push($item);

        // Put the modified Collection on the Table
        $this->Table->setData($Collection);

        $this->Table->addFieldReplacement('birthday', '<strong>{birthday}</strong>');
        $this->Table->addLinkingPattern('name', '/my/url');

        $dom = HtmlDomParser::str_get_html($this->Table->makeView()->render());
        $tds = $dom->find('td');

        $data = $Collection->shift();
        foreach($tds as $td){
            if($td->{'data-field'} == 'birthday'){
                // Value cleaned with e()
                $this->assertEquals('<strong>'.e($data[$td->{'data-field'}]).'</strong>', trim($td->innertext));
            }elseif($td->{'data-field'} == 'nname'){
                // Value cleaned with e()
                $this->assertEquals('<a href="/my/url">'.e($data[$td->{'data-field'}]).'</a>', trim($td->innertext));
            }
        }
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
