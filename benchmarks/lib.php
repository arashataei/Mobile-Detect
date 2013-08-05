<?php

class Bench_Stopwatch
{
    protected $ticks = array();

    protected static $timers = array();

    public static function timer($name)
    {
        if (!isset(self::$timers[$name])) {
            self::$timers[$name] = new self;
        }

        //load the timer
        return self::$timers[$name];
    }

    public function tick($name)
    {
        if (!count($this->ticks)) {
            throw new RuntimeException('This timer has not yet been started. You cannot mark a tick without starting.');
        }

        if (count($this->ticks) > 1 && is_double($this->ticks[count($this->ticks)-1])) {
            throw new RuntimeException('This timer has already been ended. You cannot mark a tick after ending.');
        }

        $this->ticks[] = array('event' => $name, 'time' => microtime(true));
        return $this;
    }

    public function start()
    {
        if (count($this->ticks)) {
            throw new RuntimeException('This timer has already been started. You cannot start more than once.');
        }

        $this->ticks[] = microtime(true);
        return $this;
    }

    public function end()
    {
        if (!count($this->ticks)) {
            throw new RuntimeException('This timer has not yet been started. You cannot end without starting.');
        }

        if (count($this->ticks) > 1 && is_double($this->ticks[count($this->ticks)-1])) {
            throw new RuntimeException('This timer has already been ended. You cannot end more than once.');
        }

        $this->ticks[] = microtime(true);
        return $this;
    }

    public function reset()
    {
        unset($this->ticks);
        $this->ticks = array();
    }

    public function rundown()
    {
        if (!count($this->ticks)) {
            return null;
        }

        $ticks = $this->ticks;

        $start = $ticks[0];
        $end = $ticks[count($ticks)-1];

        $counter = array(
            array('time' => 0, 'event' => 'start')
        );

        for ($i = 1; $i < count($ticks) - 1; $i++) {
            $tick = $ticks[$i];
            $counter[] = array('time' => ($tick['time'] - $start), 'event' => $tick['event']);
        }

        $counter[] = array('time' => ($end-$start), 'event' => 'end');

        return $counter;
    }
    
    public function elapsed()
    {
        if (count($this->ticks) < 2) {
            return null;
        }

        return $this->ticks[count($this->ticks)-1] - $this->ticks[0];
    }

    /** 
     * Retrieves the number of ticks, excluding start and end.
     */
    public function tickCount()
    {
        if (count($this->ticks) < 3) {
            return 0;
        }

        return count($this->ticks) - 2;
    }
}

class Bench_Data
{
    protected $header = array();
    protected $rows   = array();
    
    public function __construct(array $header = null)
    {
        $this->header = $header;
    }

    public function setHeader(array $header)
    {
        $this->header = $header;
    }

    public function addRow(array $row)
    {
        $this->rows[] = $row;
    }

    public function escapeFields(array $fields)
    {
        $newFields = array();
        foreach ($fields as $key => $val) {
            $newFields[$key] = $this->escapeField($val);
        }
        return $newFields;
    }

    public function escapeField($field)
    {
        if (is_null($field)) {
            return '';
        }
        
        if (is_bool($field)) {
            return ($field ? 'true' : 'false');
        }
        
        if (is_numeric($field)) {
            return $field;
        }
        
        if (is_object($field)) {
            if (method_exists($field, '__toString')) {
                $field = $field->__toString();
            } else {
                return '"(object)"';
            }
        }
        
        if (is_array($field)) {
            $field = implode(', ', $field);
        }
        
        return '"' . str_replace('"', '""', $field) . '"';
    }

    public function __toString()
    {
        if (is_array($this->header)) {
            $str = implode(',', $this->escapeFields($this->header)) . PHP_EOL;
        } else {
            $str = '';
        }

        foreach ($this->rows as $key => $val) {
            $str .= implode(',', $this->escapeFields($val)) . PHP_EOL;
        }

        return $str;
    }
}

class Bench_UserAgentData
{
    protected static $file = '../tests/ualist.json';
    protected static $json;
    
    public static function loadJsonData()
    {
        if (self::$json) {
            return self::$json;
        }
        
        $absFile = dirname(__FILE__) . '/' . self::$file;
        
        if (!file_exists($absFile) || !is_readable($absFile)) {
            throw new RuntimeException("Could not find $absFile for the user agent list.");
        }
        
        $contents = file_get_contents($absFile);
        $json = @json_decode($contents, true);

        if (!$json) {
            throw new RuntimeException("The $absFile doesn't contain valid JSON data.");
        }
        
        if (array_key_exists('hash', $json)) {
            //probably still in the hash format
            if (isset($json['user_agents'])) {
                $json = $json['user_agents'];
            } else {
                throw new RuntimeException("Unexpected JSON format. Please fix the JSON file or "
                    . "modify Bench_UserAgentData::loadJsonData");
            }
        }
        
        self::$json = $json;
        return $json;
    }
}
