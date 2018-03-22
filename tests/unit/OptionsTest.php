<?php

declare(strict_types=1);

namespace Nickwest\EloquentForms\Test\unit;

use Nickwest\EloquentForms\Options;
use Nickwest\EloquentForms\Test\TestCase;
use Nickwest\EloquentForms\Exceptions\InvalidOptionException;

class OptionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->Options = new Options;
    }

    public function test_options_starts_empty(): void
    {
        $this->assertAttributeEmpty('options', $this->Options);
    }

    public function test_options_magic_method_set_works(): void
    {
        $this->Options->test = 1;
        $this->Options->test2 = 2;

        $expected = ['test' => 1, 'test2' => 2];

        $this->assertAttributeEquals($expected, 'options', $this->Options);
    }

    public function test_options_magic_method_get_works(): void
    {
        $this->Options->test = 'Yippee!';

        $this->assertEquals('Yippee!', $this->Options->test);
    }

    public function test_options_magic_method_get_throws_exception_on_invalid_key(): void
    {
        $this->Options->test = 'Yippee!';

        $this->expectException(InvalidOptionException::class);
        $test = $this->Options->bob;
    }

    public function test_magic_method_isset_works(): void
    {
        $this->Options->test = 'Yippee!';

        $this->assertTrue(isset($this->Options->test));
        $this->assertFalse(isset($this->Options->bob));
    }

    public function test_options_magic_method_unset_works(): void
    {
        $this->Options->test = 'Yippee!';

        unset($this->Options->test);

        $this->expectException(InvalidOptionException::class);
        $test = $this->Options->test;
    }

    public function test_options_magic_method_unset_throws_exception_on_invalid_key(): void
    {
        $this->Options->test = 'Yippee!';

        $this->expectException(InvalidOptionException::class);
        unset($this->Options->bob);
    }

    public function test_getOptions_returns_valid_options(): void
    {
        $this->Options->test = 'Yippee!';
        $this->Options->{'2'} = 'Two';

        $expected = ['test' => 'Yippee!', '2' => 'Two'];

        $this->assertEquals($expected, $this->Options->getOptions());
    }

    public function test_getOption_returns_the_option(): void
    {
        $this->Options->test = 'Yippee!';

        $this->assertEquals('Yippee!', $this->Options->getOption('test'));
    }

    public function test_setOption_sets_the_option(): void
    {
        $this->Options->setOption('test', 1);
        $this->Options->setOption('test2', 2);

        $expected = ['test' => 1, 'test2' => 2];

        $this->assertAttributeEquals($expected, 'options', $this->Options);
    }

    public function test_hasOption_determines_if_option_exists(): void
    {
        $this->Options->test = 'Yippee!';

        $this->assertTrue($this->Options->hasOption('test'));
        $this->assertFalse($this->Options->hasOption('bob'));
    }

    public function test_removeOption_removes_the_option(): void
    {
        $this->Options->test = 'Yippee!';

        $this->Options->removeOption('test');

        $this->expectException(InvalidOptionException::class);
        $test = $this->Options->test;
    }

    public function test_setDisabledOptions_sets_options_to_be_disabled(): void
    {
        $this->Options->test = 'Yippee!';
        $this->Options->setDisabledOptions(['test']);

        $this->assertAttributeEquals(['test'], 'disabled_options', $this->Options);
    }

    public function test_setDisalbedOptions_throws_an_exception_if_invalid_options_are_passed(): void
    {
        $test_options = ['1' => 'one', '2' => 'two', '44' => 'Fourtyfour'];
        $this->Options->setOptions($test_options);

        $this->expectException(InvalidOptionException::class);
        $this->Options->setDisabledOptions(['1', '44', '4']);
    }
}
