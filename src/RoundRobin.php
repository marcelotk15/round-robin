<?php

namespace Laravel\RoundRobin;


use Laravel\RoundRobin\Objects\Schedule;

class RoundRobin
{
    /**
     * @var array Contains teams used to generate schedule
     */
    protected $teams = [];
    /**
     * @var int|null How many rounds to generate
     */
    protected $rounds = null;

    /**
     * @var int|null Seed to use for shuffle
     */
    protected $seed = null;

    /**
     * @var bool Whether to shuffle the teams or not
     */
    protected $shuffle = true;

    /**
     * Set teams and rounds at construction
     *
     * @param array $teams
     * @param int|null $rounds
     */
    public function __construct(array $teams = [])
    {
        $this->setTeams($teams);
    }

    /**
     * Set teams
     *
     * @param array $teams
     * @return void
     */
    public function setTeams(array $teams)
    {
        $this->teams = $teams;
    }

    /**
     * Make a double rounds with home and away games
     *
     * @return void
     */
    public function doubleRoundRobin()
    {
        $this->rounds = (($count = count($this->teams)) % 2 === 0 ? $count - 1 : $count) * 2;
    }

    /**
     * Shuffle array when generating schedule with optional seed
     *
     * @param int|null $seed
     * @return void
     */
    public function shuffle(int $seed = null)
    {
        $this->shuffle = true;
        $this->seed = $seed;
    }

    /**
     * Do not shuffle array when generating schedule, resets seed
     *
     * @return void
     */
    public function doNotShuffle()
    {
        $this->shuffle = false;
        $this->seed = null;
    }

    function make_schedule(array $teams, int $rounds = null, bool $shuffle = true, int $seed = null): array
    {
        $teamCount = count($teams);
        if($teamCount < 2) {
            return [];
        }
        //Account for odd number of teams by adding a bye
        if($teamCount % 2 === 1) {
            array_push($teams, null);
            $teamCount += 1;
        }
        if($shuffle) {
            //Seed shuffle with random_int for better randomness if seed is null
            srand($seed ?? random_int(PHP_INT_MIN, PHP_INT_MAX));
            shuffle($teams);
        } elseif(!is_null($seed)) {
            //Generate friendly notice that seed is set but shuffle is set to false
            trigger_error('Seed parameter has no effect when shuffle parameter is set to false');
        }
        $halfTeamCount = $teamCount / 2;
        if($rounds === null) {
            $rounds = $teamCount - 1;
        }
        $schedule = [];
        for($round = 1; $round <= $rounds; $round += 1) {
            foreach($teams as $key => $team) {
                if($key >= $halfTeamCount) {
                    break;
                }
                $team1 = $team;
                $team2 = $teams[$key + $halfTeamCount];
                //Home-away swapping
                $matchup = $round % 2 === 0 ? [$team1, $team2] : [$team2, $team1];
                $schedule[$round][] = $matchup;
            }
            $this->rotate($teams);
        }

        // If have a match with only a team will remove it from array (if number of the teams is odd)
        $empty = false;
        foreach ($schedule as $keyA => $array) {
            foreach ($array as $keyB => $matchs) {
                foreach ($matchs as $team) {
                    if ($team == '')
                    {
                        $empty = true;
                    }
                }
                if ($empty == true)
                {
                    unset($schedule[$keyA][$keyB]);
                    $empty = false;
                }
            }
        }
        return $schedule;
    }

    /**
     * Rotate array items according to the round-robin algorithm
     *
     * @param array $items
     * @return void
     */
    function rotate(array &$items)
    {
        $itemCount = count($items);
        if($itemCount < 3) {
            return;
        }
        $lastIndex = $itemCount - 1;
        /**
         * Though not technically part of the round-robin algorithm, odd-even
         * factor differentiation included to have intuitive behavior for arrays
         * with an odd number of elements
         */
        $factor = (int) ($itemCount % 2 === 0 ? $itemCount / 2 : ($itemCount / 2) + 1);
        $topRightIndex = $factor - 1;
        $topRightItem = $items[$topRightIndex];
        $bottomLeftIndex = $factor;
        $bottomLeftItem = $items[$bottomLeftIndex];
        for($i = $topRightIndex; $i > 0; $i -= 1) {
            $items[$i] = $items[$i - 1];
        }
        for($i = $bottomLeftIndex; $i < $lastIndex; $i += 1) {
            $items[$i] = $items[$i + 1];
        }
        $items[1] = $bottomLeftItem;
        $items[$lastIndex] = $topRightItem;
    }

    public function build(): Schedule
    {
        return new Schedule($this->make_schedule($this->teams, $this->rounds, $this->shuffle, $this->seed));
    }
}