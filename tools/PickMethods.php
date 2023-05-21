<?php

$classFile = './src/class/FrequencyTable.php';

$lines = file($classFile);
$className = null;
$methods = [];
$methodNames = [];
$patternClassName = '/class\s+([A-Za-z0-9_]+)\s+/';
$patternMethod = '/^\s*public\s+function\s+([A-Za-z0-9_]+\(.*\))/';

foreach($lines as $line) {
    $matches = [];
    if (preg_match($patternClassName, $line, $matches)) {
        $className = $matches[1];
        continue;
    }
    if (preg_match($patternMethod, $line, $matches)) {
        $methods[] = "" . $matches[1];
        $methodNames[] = preg_replace('/\(.*\)/','',$matches[1]);
    }
}
$lines = null;
echo "# Class: " . $className . "\n\n";
echo "## Methods\n\n";
echo "<details><summary>list</summary>\n\n";
foreach ($methodNames as $index => $methodName) {
    echo "- [" . $methodName . "()](#" . strtolower($methodName) . ")\n";
}
echo "</details>\n\n";
foreach($methods as $index => $method) {
    echo "### " . $methodNames[$index] . "\n\n";
    echo "```php\n" . $method . "\n```\n\n";
    echo "#### Parameter\n\n";
    echo "#### Example\n\n";
}
