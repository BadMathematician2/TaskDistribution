<?php


namespace App;



class TasksDistribution
{
    /**
     * масив усуї завдань
     * @var string[]
     */
    protected $tasks;
    /**
     * масив усіх тих, хто може розв'язувати завдання
     * @var string[]
     */
    protected $instants;
    /**
     * масив із часу потрібного на відповідне завдання
     * @var int[]
     */
    protected $times;
    /**
     * максимальний час, який може пропустити завдання поза чергою
     * @var int
     */
    protected $max_time;
    /**
     * час виконанння всіх завдань
     * @var float|int
     *
     */
    protected $time_sum;
    /**
     * асоціативний масив, яключ - це завдання, а значення - час потріьний на його виконання
     * @var array
     */
    protected $tasks_n_times;
    /**
     * масив результатів
     * елементами є масиви із завдань і їх номерів, які слід виконати
     * у відповідному instanti
     * @var array
     */
    protected $result;
    /**
     * мінімальний час, який слід виконувати кожному інстанті
     * це весь час завдань, поділений на кількість виконавців
     * @var float
     */
    protected $time;
    /**
     *
     * @return float|int
     */
    public function getTimeSum()
    {
        return $this->time_sum;
    }
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
        $this->time = array_sum($this->times)/sizeof($this->instants);
        $this->result = array_fill(0,sizeof($this->instants),[]);
    }
    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }
    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }
    public function distribute()
    {
        $T = ceil($this->time);
        $b = true;  //показує чи є завдання із часом виконання, меншим ніж sizeof($this->instants)
        $t = array_fill(0,sizeof($this->instants),0); //масив, елементами якого є час виконання завдань відповідного виконавця завдань

        for ($i = 0; $i < sizeof($this->instants); $i++)    //проходження по всім виконавцям завдань
        {
            while ($t[$i] < $T && sizeof($this->times)>0 && $b)  //перша умова - це поки час усіх завдань менший за середній час, друга - поки є завдання, третя - див комент вище
            {
                $j = $this->getNearestNumber($this->times, $T - $t[$i]);  //номер найбільшого елемента, із яким час усіх завдань в виконавці не буде більшим ніж середній час
                if ($j === -1) //якщо $j === -1, то немає завдання із часом виконання, меншим ніж sizeof($this->instants)
                    $b = false;
                else {
                    $this->result[$i] += [$j => $this->tasks[$this->getNearestNumber($this->times, $T - $t[$i])]];
                    $t[$i] += $this->times[$this->getNearestNumber($this->times, $T - $t[$i])];
                    unset($this->tasks[$j]);
                    unset($this->times[$j]);
                }
            }
            $b = true;
            ksort($this->result[$i]);
        }
        $m = sizeof($this->times);
        for ($i = 0; $i < $m; $i++)  //запис тих завдань, що залишилися до тих виконавців,так, щоб завдання із найбільшим часом було у завдання із найманшим часом
        {
            $j = array_search(max($this->times),$this->times);  //номер завдання із найбфльшим часом
            $number_instans = array_search(min($t),$t); //номер виконавця із найменшим сумарним часом
            $this->result[$number_instans] += [$j => $this->tasks[$j]];
            $t[$number_instans] += $this->times[$j];
            unset($this->tasks[$j]);
            unset($this->times[$j]);
            ksort($this->result[$number_instans]);
        }
    }

    private function getNearestNumber($a, $n)
    {
        $result = -1;
        $val = min($a);
        foreach ($a as $key => $value) {
            if ($value <= $n && $val < $value) {
                $val = $value;
                $result = $key;
            }
        }

        return $result;
    }

    public function sumResult()
    {
        $sum = array_fill(0,sizeof($this->instants),0);
        for ($i = 0; $i < sizeof($this->instants); $i++)
        {
            foreach ($this->result[$i] as $key=>$value)
                $sum[$i] += $this->tasks_n_times[$value];
        }

        return $sum;
    }

    private function writeResult($j,$T,$t)
    {
        $this->result[$i] += [$j => $this->tasks[$this->getNearestNumber($this->times, $T - $t[$i])]];
        $t[$i] += $this->times[$this->getNearestNumber($this->times, $T - $t[$i])];
        unset($this->tasks[$j]);
        unset($this->times[$j]);
    }



}
