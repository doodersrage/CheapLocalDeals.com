<?php
/**
 * A combined getter and setter for use in objects.
 * (Borrowed from Ollie Saunders)
 *
 * @see README for usage
 * @param object $obj $this
 * @param mixed &$property property to set and get
 * @param array $args
 * @return mixed
 */
function accessor($obj, &$property, array $args = array())
{
    if (empty($args)) {
        return $property;
    }
    $property = $args[0];
    return $obj;
}

/**
 * This is a small unit testing / speed testing framework to be used with simpletest
 * I wrote this for use with PHP CSV Utilities but I imagine it will be useful for
 * just about any PHP project
 */
class TestInterface
{
    /**
     * This is the directory where your unit tests are
     *
     * @access protected
     * @var string
     */
    protected $testdir;
    /**
     * This is the directory where your library files are
     *
     * @access protected
     * @var string
     */
    protected $libdir;
    /**
     * Point this at the directory where your tests are. Then you can load the tests by doing
     * TestInterface::load("Test_Name") and if you have named your files right (Test/Name.php)
     * it will work for ya.
     */
    public function __construct($testdir, $libdir) {
    
        $this->testdir($testdir);
        $this->libdir($libdir);
    
    }
    
    public function testdir() {
    
        $args = func_get_args();
        if (isset($args[0])) $args[0] = realpath($args[0]);
        return accessor($this, $this->testdir, $args);
    
    }
    
    public function libdir() {
    
        $args = func_get_args();
        if (isset($args[0])) $args[0] = realpath($args[0]);
        return accessor($this, $this->libdir, $args);
    
    }
    
    public function getTests() {
    
        $tests = array();
        $dir = new RecursiveDirectoryIterator($this->testdir());
        foreach ($dir as $entry) {
            //if ($entry->isDot()) continue;
            //if ($entry->isDir()) continue;
            $tests[] = $entry->getFileName();
        }
        return $tests;
    
    }
}

class StopWatch
{
    protected $start, $stop;
    public function start() {
    
        $this->start = microtime(true);
    
    }
    
    public function stop() {
    
        $this->stop = microtime(true);
    
    }
    
    public function result() {
    
        if ($this->start && $this->stop) {
            return ($this->stop - $this->start);
        }
        return false;
    
    }
}
/*
$stopwatch = new StopWatch();
$stopwatch->start();
sleep(10);
$stopwatch->stop();
printf("<p>%s</p>", $stopwatch->result());
*/
$tester = new TestInterface('./TestCases', '../../Csv');

foreach ($tester->getTests() as $test) {
    print_r($test . "<BR>");
}

echo phpversion();