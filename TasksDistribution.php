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
    }

    protected $result;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }



    public function distribute()
    {   $n = sizeof($this->instants);
        $T = ceil(array_sum($this->times)/$n);

        $a = array();
        $t = array_fill(0,sizeof($this->instants),0);
        //dd($T);
        $this->result = array_fill(0,$n,[]);
        for ($i = 0; $i < sizeof($this->instants); $i++)
        {
            while ($t[$i] < $T && sizeof($this->times)>0)
            {
                $j = $this->equel_or_less($this->times, $T - $t[$i]);
                $this->result[$i] += [$j =>  $this->tasks[$this->equel_or_less($this->times, $T - $t[$i])]     ];
                $t[$i] += $this->times[$this->equel_or_less($this->times, $T - $t[$i])];
                //dd($t[$i]);
                unset($this->tasks[$j]);
                unset($this->times[$j]);


            }
            ksort($this->result[$i]);
        }

    }


    public function distribute_by_order()
    {

        $n = sizeof($this->instants);
        $this->result = array_fill(0,$n,'');
        for ($i = 0; $i < sizeof($this->tasks); $i++)
        {
            $this->result[$i%$n] = $this->result[$i%$n]  . $this->tasks[$i] . ';';
        }
    }

    public function distribute_by()
    {
        $a = array();
        $t = array_fill(0,sizeof($this->instants),0);
        $n = sizeof($this->instants);
        $this->result = array_fill(0,$n,'');
        for ($i = 0; $i < sizeof($this->tasks); $i++)
        {
            if ($this->times[$i%$n] + $t[$i%$n] <= $this->max_time) {
                $this->result[$i % $n] = $this->result[$i % $n] . $this->tasks[$i] . ';';
                $t[$i%$n] += $this->times[$i];
            }
            else array_push($a,$this->tasks[$i]);
        }
        //dd($this->equel_or_less($this->times,6));
        for ($i = 0; $i < sizeof($a); $i++)
            $this->result[$i % $n] = $this->result[$i % $n] . $a[$i] . ';';
    }


    private function equel_or_less($a,$n)
    {
        $result = 0;
        $val = min($a);
        foreach ($a as $key => $value) {
            if ($value <= $n && $val < $value) {
                $val = $value;
                $result = $key;
            }
        }
        if ($val == min($a))
            foreach ($a as $key => $value)
                if ($value == $val)
                    $result = $key;

        return $result;
    }



}
