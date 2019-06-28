<?php

use Orchestra\Testbench\TestCase;

class RR_Test extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [Teka\RoundRobin\RoundRobinServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'RoundRobin' => Teka\RoundRobin\RoundRobinFacade::class,
        ];
    }

    public function test_if_instanciate()
    {
        $instance = RoundRobin::from(['Arsenal', 'Bayer', 'Barcelona', 'Juventus']);
        $this->assertInstanceOf(Laravel\RoundRobin\RoundRobin::class, $instance);
    }

    public function test_if_throws_an_exception()
    {
        $this->expectException(Exception::class);

        RoundRobin::from(['um'])->make();
    }

    public function test_if_double_rounds_works()
    {
        $schedule = RoundRobin::from(['Arsenal', 'Bayer', 'Barcelona', 'Juventus'])
                                ->doubleRoundRobin()
                                ->make();

        $this->assertEquals(count($schedule), 6);
    }

    public function test_if_a_custom_number_of_rounds_works()
    {
        $schedule = RoundRobin::from([
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
        $schedule = RoundRobin::from([
            'Arsenal',
            'Bayer',
            'Barcelona',
            'Juventus',
            'Milan',
            'PSG',
            'Inter',
            'Real Madrid',
        ])->doNotShuffle()->make();

        $this->assertEquals(array_first($schedule), [
            ['Milan', 'Arsenal'],
            ['PSG', 'Bayer'],
            ['Inter', 'Barcelona'],
            ['Real Madrid', 'Juventus'],
        ]);

        $this->assertEquals(array_slice($schedule, 1, 1), [[
            ['Arsenal', 'PSG'],
            ['Milan', 'Inter'],
            ['Bayer', 'Real Madrid'],
            ['Barcelona', 'Juventus'],
        ]]);
    }
}
