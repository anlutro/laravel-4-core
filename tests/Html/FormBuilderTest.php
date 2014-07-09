<?php
namespace anlutro\Core\Tests\Html;

use PHPUnit_Framework_TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteCollection;
use Illuminate\Html\HtmlBuilder;
use anlutro\Core\Html\FormBuilder;

class FormBuilderTest extends PHPUnit_Framework_TestCase
{
	protected function makeFormBuilder()
	{
		$urlGenerator = new UrlGenerator(new RouteCollection, Request::create('/foo', 'GET'));
		$htmlBuilder = new HtmlBuilder($urlGenerator);
		return new FormBuilder($htmlBuilder, $urlGenerator, '');
	}

	/** @test */
	public function getsNestedObjectsProperly()
	{
		$obj = new \StdClass;
		$obj->stuff = new \Illuminate\Support\Collection([5 => ['bar' => 'baz']]);
		$model = ['foo' => $obj];
		$form = $this->makeFormBuilder();
		$form->setModel($model);
		$this->assertEquals('baz', $form->value('foo[stuff][5][bar]'));
		$this->assertContains('value="baz"', $form->text('foo[stuff][5][bar]'));
	}

	/** @test */
	public function alwaysSetsIdAttribute()
	{
		$form = $this->makeFormBuilder();
		$this->assertContains('id="foo"', $form->text('foo'));
	}

	/** @test */
	public function checkedStateCanBeOverridden()
	{
		$model = ['foo' => true, 'bar' => false];
		$form = $this->makeFormBuilder();
		$this->assertNotContains('checked', $form->checkbox('foo', 1, false));
		$this->assertContains('checked', $form->checkbox('foo', 1, true));
	}
}
