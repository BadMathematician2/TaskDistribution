<?php


namespace App;



class TasksDistribution
{
    /**
     * @var string[]
     */
    protected $tasks;
    /**
     * @var string[]
     */
    protected $instants;
    /**
     * @var int[]
     */
    protected $times;
    /**
     * @var int
     */
    protected $max_time;

    protected $time_sum;

    /**
     * @return float|int
     */
    public function getTimeSum()
    {
        return $this->time_sum;
    }

    protected $tasks_n_times;
    /**
     * TasksDistribution constructor.
     * @param $tasks string[]
     * @param $instants string[]
     * @param $times int[]
     * @param $max_time int
     */
    public function __construct($tasks, $instants, $times, $max_time)
    {
        $this->tasks = $tasks;
        $this->instants = $instants;
        $this->times = $times;
        $this->max_time = $max_time;
        $this->tasks_n_times = [];
        for ($i=0; $i<sizeof($tasks); $i++)
            $this->tasks_n_times += [$this->tasks[$i] => $this->times[$i]];
        $this->time_sum = array_sum($this->times);
    }

    protected $result;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    protected $time;

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    public function distribute()
    {   $n = sizeof($this->instants);
        $T = ceil(array_sum($this->times)/$n);
        $this->time = array_sum($this->times)/$n;
        $b = true;
        $t = array_fill(0,sizeof($this->instants),0);
        //dd($T);
        $this->result = array_fill(0,$n,[]);
        for ($i = 0; $i < sizeof($this->instants); $i++)
        {
            while ($t[$i] < $T && sizeof($this->times)>0 && $b)
            {
                $j = $this->equel_or_less($this->times, $T - $t[$i]);
                if ($j === -1)
                    $b = false;
                else {
                    $this->result[$i] += [$j => $this->tasks[$this->equel_or_less($this->times, $T - $t[$i])]];
                    $t[$i] += $this->times[$this->equel_or_less($this->times, $T - $t[$i])];
                    //dd($t[$i]);
                    unset($this->tasks[$j]);
                    unset($this->times[$j]);
                }

            }
            $b = true;
            ksort($this->result[$i]);
        }
        $m = sizeof($this->times);
        for ($i = 0; $i < $m; $i++)
        {
            $j = array_search(min($this->times),$this->times);
            //dd($j);
            $number_instans = array_search(min($t),$t);
            $this->result[$number_instans] += [$j => $this->tasks[$j]];
            $t[$number_instans] += $this->times[$j];
            unset($this->tasks[$j]);
            unset($this->times[$j]);
            ksort($this->result[$i]);
        }


    }

    private function equel_or_less($a,$n)
    {
        $result = -1;
        $val = min($a);
        foreach ($a as $key => $value) {
            if ($value <= $n && $val < $value) {
                $val = $value;
                $result = $key;
            }
        }
        /*if ($val == min($a))
            foreach ($a as $key => $value)
                if ($value == $val)
                    $result = $key;*/

        return $result;
    }

    public function sum_result()
    {
        $sum = array_fill(0,sizeof($this->instants),0);
        for ($i = 0; $i < sizeof($this->instants); $i++)
        {
            foreach ($this->result[$i] as $key=>$value)
                $sum[$i] += $this->tasks_n_times[$value];
        }
        return $sum;
    }



}
