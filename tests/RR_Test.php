<?php

/**
 * Created by PhpStorm.
 * User: Jorge
 * Date: 23/04/2017
 * Time: 20:25
 */

use Laravel\RoundRobin\RoundRobinFacade as RoundRobin;
use Orchestra\Testbench\TestCase;

class RR_Test extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [Laravel\RoundRobin\RoundRobinServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'RoundRobin' => Laravel\RoundRobin\RoundRobinFacade::class
        ];
    }

    public function test_if_instanciate()
    {
        $instance = RoundRobin::teams(['Arsenal', 'Bayer', 'Barcelona', 'Juventus']);
        $this->assertInstanceOf(Laravel\RoundRobin\RoundRobin::class, $instance);
    }

    public function test_if_throws_an_exception()
    {
        $this->expectException(Exception::class);

        RoundRobin::teams(['um'])->make();

    }

    public function test_if_double_rounds_works()
    {
        $schedule = RoundRobin::teams(['Arsenal', 'Bayer', 'Barcelona', 'Juventus'])
                                ->doubleRoundRobin()
                                ->make();

        $this->assertEquals(count($schedule), 6);


    }

    public function test_if_a_custom_number_of_rounds_works()
    {
        $schedule = RoundRobin::teams([
            'Arsenal',
            'Bayer',
            'Barcelona',
            'Juventus',
            'Milan',
            'PSG',
            'Inter',
//            'Real Madrid',
        ])->make();

        $this->assertEquals(count($schedule), 7);
    }

    public function test_if_do_not_shuffle()
    {
        $schedule = RoundRobin::teams([
            'Arsenal',
            'Bayer',
            'Barcelona',
            'Juventus',
            'Milan',
            'PSG',
            'Inter',
            'Real Madrid',
        ])->doNotShuffle()->make();

        $this->assertEquals(array_first($schedule),[
            ['Milan', 'Arsenal'],
            ['PSG', 'Bayer'],
            ['Inter', 'Barcelona'],
            ['Real Madrid', 'Juventus'],
        ]);

        $this->assertEquals(array_slice($schedule,1,1),[[
            ['Arsenal', 'PSG'],
            ['Milan', 'Inter'],
            ['Bayer', 'Real Madrid'],
            ['Barcelona', 'Juventus'],
        ]]);
    }
}
