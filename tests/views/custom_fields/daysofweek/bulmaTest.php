<?php

namespace Nickwest\EloquentForms\Test\view\defaults;

use Faker;

use Illuminate\Support\Collection;
use KubAT\PhpSimple\HtmlDomParser;

use Nickwest\EloquentForms\Field;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Themes\bulma\Theme as Bulma;
use Nickwest\EloquentForms\CustomFields\daysofweek\CustomField as DaysOfWeekField;

class bulmaTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->Field = new Field('days');

        $this->Field->label = 'Best Days';
        $this->Field->setTheme(new Bulma());

        $this->Field->CustomField = new DaysOfWeekField();

        $this->days = ['M' => 'Mon', 'T' => 'Tue', 'W' => 'Wed', 'R' => 'Thu', 'F' => 'Fri', 'S' => 'Sat', 'U' => 'Sun'];
    }

    public function test_field_creates_view()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $field = $dom->find('field-days');

        $this->assertNotFalse($field);
    }

    public function test_field_can_be_inline()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView(true)->render());
        $field = $dom->find('field-days');

        $this->assertNotFalse($field);
    }

    public function test_field_has_wrapper()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $field = $dom->find('div.daysofweek');

        $this->assertNotFalse($field);
    }

    public function test_field_has_each_day_wrapper()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $spans = $dom->find('span.' . $this->Field->options->wrapper_class);

        $this->assertEquals(7, count($spans));
    }

    public function test_field_has_each_day_label()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $labels = $dom->find('label');

        // one extra label for the group
        $this->assertEquals(8, count($labels));
    }

    public function test_field_has_all_checkboxes()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $inputs = $dom->find('input');

        foreach ($inputs as $input) {
            $this->assertEquals('checkbox', $input->type);
            $this->assertEquals('days[]', $input->name);
        }
    }

    // Display only
    public function test_field_can_be_display_only()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView(false, true)->render());
        $field = $dom->find('field-days');

        $this->assertNotFalse($field);
    }

    public function test_field_display_only_shows_no_fields()
    {
        $dom = HtmlDomParser::str_get_html($this->Field->makeView(false, true)->render());
        $inputs = $dom->find('input');

        $this->assertEmpty($inputs);
    }

    public function test_field_display_shows_given_value()
    {
        $this->Field->attributes->value = ['M', 'W', 'F'];

        $dom = HtmlDomParser::str_get_html($this->Field->makeView(false, true)->render());
        $values = $dom->find('div.daysofweek div');

        $days = [];
        foreach ($values as $value) {
            $days[] = $value->innertext;
        }
        $expected = ['Mon', 'Wed', 'Fri'];

        $this->assertEquals($expected, $days);
    }
}
