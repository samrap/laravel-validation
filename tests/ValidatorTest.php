<?php

use Mockery as m;
use Samrap\Validation\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testValidatorPasses()
    {
        $validator = $this->getValidator();

        $rules = ['foo' => 'bar'];
        $this->assertEmpty($validator->validate($rules)->errors());
    }

    public function testValidatorFails()
    {
        $errors = ['foo' => 'baz'];
        $validator = $this->getValidator($errors);

        $rules = ['foo' => 'bar'];
        $this->assertEquals($errors, $validator->validate($rules)->errors());
    }

    protected function getValidator($errors = [])
    {
        $laravelValidator = m::mock('\Illuminate\Validation\Validator');
        $laravelValidator->shouldReceive('errors')->andReturn($errors);

        $factory = m::mock('Illuminate\Validation\Factory');
        $factory->shouldReceive('make')->andReturn($laravelValidator);

        $validator = new Validator($factory);

        return $validator;
    }
}
