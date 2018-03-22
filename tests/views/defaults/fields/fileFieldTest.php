<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\view\defaults\fields;

use Sunra\PhpSimple\HtmlDomParser;
use Nickwest\EloquentForms\Test\FieldViewTestCase;
use Nickwest\EloquentForms\Test\ThemeTestInterfaces\fileFieldTestInterface;

class fileFieldTest extends FieldViewTestCase implements fileFieldTestInterface
{
    protected $test_value = 'yoda.pdf';
    protected $test_type = 'file';

    // Run all basic tests

    public function test_field_has_correct_value_attribute_when_changed(): void
    {
        $this->Field->attributes->value = $this->test_value;
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $remove_button = current($dom->find($this->test_tag));
        $file_link = current($dom->find('div[class=file-link]'));

        $this->assertEquals('Remove', $remove_button->value);
        $this->assertEquals('yoda.pdf', trim($file_link->plaintext));
    }

    public function test_remove_button_can_have_a_different_value(): void
    {
        $this->Field->attributes->value = $this->test_value;
        $this->Field->file_delete_button_value = 'Obliterate';
        $dom = HtmlDomParser::str_get_html($this->Field->makeView()->render());
        $remove_button = current($dom->find($this->test_tag));

        $this->assertEquals('Obliterate', $remove_button->value);
    }
}
