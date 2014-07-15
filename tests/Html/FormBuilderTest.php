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
	protected function makeFormBuilder($model = null)
	{
		$urlGenerator = new UrlGenerator(new RouteCollection, Request::create('/foo', 'GET'));
		$htmlBuilder = new HtmlBuilder($urlGenerator);
		$formBuilder = new FormBuilder($htmlBuilder, $urlGenerator, '');
		if ($model) $formBuilder->setModel($model);
		return $formBuilder;
	}

	/** @test */
	public function getsNestedObjectsProperly()
	{
		$obj = new \StdClass;
		$obj->stuff = new \Illuminate\Support\Collection([5 => ['bar' => 'baz']]);
		$model = ['foo' => $obj];
		$form = $this->makeFormBuilder($model);
		$this->assertEquals('baz', $form->value('foo[stuff][5][bar]'));
		$this->assertContains('value="baz"', $form->text('foo[stuff][5][bar]'));
	}

	/** @test */
	public function getsEloquentObjectsProperly()
	{
		$model = new \StdClass;
		$related = [new StubModel(['id' => 1, 'name' => 'foo']), new StubModel(['id' => 2, 'name' => 'bar'])];
		$model->related = new \Illuminate\Database\Eloquent\Collection($related);
		$form = $this->makeFormBuilder($model);
		$this->assertEquals(null, $form->value('related[0][name]'));
		$this->assertEquals('foo', $form->value('related[1][name]'));
		$this->assertEquals('bar', $form->value('related[2][name]'));
		$this->assertEquals(null, $form->value('related[3][name]'));
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

class StubModel extends \Illuminate\Database\Eloquent\Model
{
	protected $guarded = [];
}
