<?php

namespace Thomaswelton\Tests\LaravelGravatar;

use Thomaswelton\LaravelGravatar\Gravatar;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class GravatarTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_check_if_a_gravatar_exists()
    {
        $config = $this->getConfig('identicon', 200, 'g');
        $gravatar = new Gravatar($config);

        $this->assertTrue($gravatar->exists('antoine.augusti@gmail.com'));
        $this->assertFalse($gravatar->exists('foobar@example.com'));
    }

    /** @test */
    public function it_can_get_the_source_of_an_existing_gravatar()
    {
        $config = $this->getConfig('monsterid', 242, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('s=242&r=pg&d=monsterid', $gravatar->src('antoine.augusti@gmail.com'));
        $this->assertContains('https://secure.gravatar.com', $gravatar->src('antoine.augusti@gmail.com'));
    }

    /** @test */
    public function it_can_override_the_size_of_an_existing_gravatar()
    {
        $config = $this->getConfig('monsterid', 200, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('s=50', $gravatar->src('antoine.augusti@gmail.com', 50));
    }

    /** @test */
    public function the_gravatar_size_cannot_go_over_2048px()
    {
        $config = $this->getConfig('monsterid', 200, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('s=512', $gravatar->src('antoine.augusti@gmail.com', 513));
    }

    /** @test */
    public function it_can_override_the_rating_of_an_existing_gravatar()
    {
        $config = $this->getConfig('monsterid', 200, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('r=g', $gravatar->src('antoine.augusti@gmail.com', null, 'g'));
    }

    /** @test */
    public function it_can_format_a_gravatar_image()
    {
        $config = $this->getConfig('monsterid', 250, 'pg');
        $gravatar = new Gravatar($config);

        $expected = '<img src="https://secure.gravatar.com/avatar/91b3b0391936c88c2d8a51754d8d3935?s=250&r=pg&d=monsterid" alt="" height="250" width="250">';

        $this->assertEquals($expected, $gravatar->image('antoine.augusti@gmail.com'));
    }

    /** @test */
    public function it_can_set_the_alt_attribute_for_a_gravatar_image()
    {
        $config = $this->getConfig('monsterid', 250, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('alt="foo"', $gravatar->image('antoine.augusti@gmail.com', 'foo'));
    }

    /** @test */
    public function it_can_override_the_dimension_for_a_gravatar_image()
    {
        $config = $this->getConfig('monsterid', 250, 'pg');
        $gravatar = new Gravatar($config);

        $dimensions = [
            'width' => 260,
            'height' => 300,
        ];

        $expected = 's=300&r=pg&d=monsterid" alt="" height="300" width="260"';

        $this->assertContains($expected, $gravatar->image('antoine.augusti@gmail.com', null, $dimensions));
    }

    /** @test */
    public function it_cannot_go_over_512px_for_a_gravatar_image()
    {
        $config = $this->getConfig('monsterid', 250, 'pg');
        $gravatar = new Gravatar($config);

        $dimensions = [
            'width' => 260,
            'height' => 513,
        ];

        $expected = 's=512&r=pg&d=monsterid" alt="" height="513" width="260"';

        $this->assertContains($expected, $gravatar->image('antoine.augusti@gmail.com', null, $dimensions));
    }

    /** @test */
    public function it_can_override_the_rating_for_a_gravatar_image()
    {
        $config = $this->getConfig('monsterid', 250, 'pg');
        $gravatar = new Gravatar($config);

        $this->assertContains('r=x', $gravatar->image('antoine.augusti@gmail.com', null, [], 'x'));
    }

    private function getConfig($default, $size, $rating)
    {
        $config = m::mock('Illuminate\Contracts\Config\Repository');
        $config->shouldReceive('get')->with('gravatar.default')->andReturn($default);
        $config->shouldReceive('get')->with('gravatar.size')->andReturn($size);
        $config->shouldReceive('get')->with('gravatar.maxRating', 'g')->andReturn($rating);

        return $config;
    }
}
