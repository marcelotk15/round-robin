<?php

namespace Laravel\RoundRobin;

use Exception;
use Laravel\RoundRobin\Objects\Schedule;

/**
 * Class RoundRobin
 * @package Laravel\RoundRobin
 */
class RoundRobin
{
    /**
     * @var array Contains teams used to generate schedule
     */
    private $_teams = [];
    /**
     * @var int|null How many rounds to generate
     */
    private $_rounds = null;

    /**
     * @var int|null Seed to use for shuffle
     */
    private $_seed = null;

    /**
     * @var bool Whether to shuffle the teams or not
     */
    private $_shuffle = true;

    /**
     * @var array
     */
    private $_schedule = [];

    /**
     * Set teams and rounds at construction.
     *
     * @param array $teams
     *
     * @internal param int|null $rounds
     */
    public function __construct(array $teams = [])
    {
        $this->_teams = $teams;
    }


    /**
     * @param array $teams
     * @return static
     * @throws Exception
     */
    public static function from(array $teams)
    {
        if (empty($teams) || count($teams) < 2) {
            throw new Exception("You need set a team array or a minimum of two teams to make this RoundRobin.");
        }

        $instance = new static($teams);
        return $instance;
    }

    /**
     * Make a double rounds with home and away games.
     *
     * @return RoundRobin
     */
    public function doubleRoundRobin()
    {
        $this->_rounds = (($count = count($this->_teams)) % 2 === 0 ? $count - 1 : $count) * 2;
        return $this;
    }

    /**
     * Shuffle array when generating schedule with optional seed.
     *
     * @param int|null $seed
     *
     * @return RoundRobin
     */
    public function shuffle(int $seed = null)
    {
        $this->_shuffle = true;
        $this->_seed    = $seed;
        return $this;
    }

    /**
     * Do not shuffle array when generating schedule, resets seed.
     *
     * @return RoundRobin
     */
    public function doNotShuffle()
    {
        $this->_shuffle = false;
        $this->_seed    = null;
        return $this;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function make()
    {
        if (empty($this->_teams) || count($this->_teams) < 2) {
            throw new Exception("You need set a team array or a minimum of two teams to make this RoundRobin.");
        }

        //Account for odd number of teams by adding a bye
        $this->checkForOdd();

        //do a shuffle if set
        $this->doShuffle();

        //make a schedule array
        $this->buildSchedule();

        // If have a match with only a team will remove it from array (if number of the teams is odd)
        $this->cleanSchedule();

        return $this->_schedule;
    }

    /**
     * Rotate array items according to the round-robin algorithm.
     *
     * @return RoundRobin
     */
    public function rotate()
    {
        $itemCount = count($this->_teams);
        if ($itemCount < 3) {
            return $this;
        }
        $lastIndex = $itemCount - 1;
        /**
         * Though not technically part of the round-robin algorithm, odd-even
         * factor differentiation included to have intuitive behavior for arrays
         * with an odd number of elements.
         */
        $factor          = (int)($itemCount % 2 === 0 ? $itemCount / 2 : ($itemCount / 2) + 1);
        $topRightIndex   = $factor - 1;
        $topRightItem    = $this->_teams[$topRightIndex];
        $bottomLeftIndex = $factor;
        $bottomLeftItem  = $this->_teams[$bottomLeftIndex];
        for ($i = $topRightIndex; $i > 0; $i -= 1) {
            $this->_teams[$i] = $this->_teams[$i - 1];
        }
        for ($i = $bottomLeftIndex; $i < $lastIndex; $i += 1) {
            $this->_teams[$i] = $this->_teams[$i + 1];
        }
        $this->_teams[1]          = $bottomLeftItem;
        $this->_teams[$lastIndex] = $topRightItem;

        return $this;
    }

    /**
     * @return Schedule
     * @throws Exception
     */
    public function makeSchedule()
    {
        if (empty($this->_teams) || count($this->_teams) < 2) {
            throw new Exception("You need set a team array or a minimum of two teams to make this RoundRobin.");
        }

        return new Schedule($this->make());
    }

    /*
     * PRIVATE FUNCTIONS
     */

    /**
     * do it a Schuffle in current _teams array if shuffle method called
     */
    private function doShuffle()
    {
        if ($this->_shuffle) {
            srand($this->_seed ?? random_int(PHP_INT_MIN, PHP_INT_MAX));
            shuffle($this->_teams);
        }
    }

    /**
     * Checks for odd number of items in _teams array
     */
    private function checkForOdd()
    {
        if (count($this->_teams) % 2 === 1) {
            array_push($this->_teams, null);
        }
    }

    /**
     * Builds the _schedule array
     * @return $this
     */
    private function buildSchedule()
    {
        $halfTeamCount = count($this->_teams) / 2;

        $rounds = $this->_rounds ?? count($this->_teams) - 1;

        for ($round = 1; $round <= $rounds; $round += 1) {
            foreach ($this->_teams as $key => $team) {
                if ($key >= $halfTeamCount) {
                    break;
                }
                $team1 = $team;
                $team2 = $this->_teams[$key + $halfTeamCount];
                //Home-away swapping
                $matchup                   = $round % 2 === 0 ? [$team1, $team2] : [$team2, $team1];
                $this->_schedule[$round][] = $matchup;
            }
            $this->rotate();
        }

        return $this;
    }

    /**
     * Perform a search for alone items in the _schedule
     * and corrects with missed rounds
     * @return $this
     */
    private function cleanSchedule()
    {
        $_schedule = array_map(function ($round) {
            $values = array_map(function ($match) {
                return array_filter($match, function () use ($match){
                    return !in_array(null, $match) ?? $match;
                });
            }, $round);
            $matches = array_filter($values);
            return array_values($matches);
        }, $this->_schedule);

        $this->_schedule = $_schedule;

        return $this;
    }
}
