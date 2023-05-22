<?php
require_once('../vendor/autoload.php');

use Intervention\Image\ImageManagerStatic as Image;

class Histogram
{
    private $ft;
    private $image;
    private $canvasWidth = 400;
    private $canvasHeight = 300;
    private $canvasBackgroundColor = '#ffffff';
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 2;
    private $gridColor = '#333333';
    private $gridWidth = 1;
    private $gridHeightPitch;
    private $barWidth;
    private $barHeightPitch;
    private $barBackgroundColor = '#0000ff';
    private $barBorderColor = '#9999ff';
    private $barBorderWidth = 1;
    private $frequencyPolygonColor = '#ff0000';
    private $frequencyPolygonWidth = 2;
    private $cumulativeRelativeFrequencyPolygonColor = '#33ff66';
    private $cumulativeRelativeFrequencyPolygonWidth = 2;
    private $classColor = '#333333';
    private $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //private $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    private $fontSize = 16;
    private $barMaxValue;
    private $barMinValue;
    private $baseX;
    private $baseY;
    private $parsed = [];
    private $configValidation = [
        'barHeigtPitch' => 'integer|min:1',
        'canvasWidth' => 'integer|min:100|max:1920',
        'canvasHeight' => 'integer|min:100|max:1080',
        'canvasBackgroundColor' => 'colorcode',
        'frameXRatio' => 'float|min:0.5|max:1.0',
        'frameYRatio'=> 'float|min:0.5|max:1.0',
        'axisColor' => 'colorcode',
        'axisWidth' => 'integer|min:1',
        'gridColor' => 'colorcode',
        'gridWidth' => 'integer|min:1',
        'gridHeightPitch' => 'integer|min:1',
        'barBackgroundColor' => 'colorcode',
        'barBorderColor' => 'colorcode',
        'barBorderWidth' => 'integer:min:1',
        'frequencyPolygonColor' => 'colorcode',
        'frequencyPolygonWidth' => 'integer|min:1',
        'cumulativeRelativeFrequencyPolygonColor' => 'colorcode',
        'cumulativeRelativeFrequencyPolygonWidth' => 'integer|min:1',
        'classColor' => 'colorcode',
        'fontPath' => 'file',
        'fontSize' => 'integer|min:6',
    ];
    private $configValidationWarning = [];
    private $configValidationError = [];

    public function __construct() {
        Image::configure(['driver' => 'imagick']);
    }

    public function getValidConfig($config) {
        $this->configValidationWarning = [];
        $this->configValidationError = [];
        if (!is_array($config)) {
            $this->configValidationError['isArray'] = '$config is not array.';
            return [];
        }
        if (empty($config)) {
            $this->configValidationWarning['count'] = '$config is empty.';
            return [];
        }
        $acceptableKeys = array_keys($this->configValidation);
        $validConfig = [];
        foreach($config as $key => $value) {
            if (!in_array($key, $acceptableKeys)) continue;
            //if ($this->validateConfigKey($key,$value)) $validConfig[$key] = $value;
            if ($this->validateConfig($key, $value)) $validConfig[$key] = $value;
        }
        return $validConfig;
    }

    public function validateConfig($key, $value) {
        if (!strlen($this->configValidation[$key])) return false;
        $conditions = explode('|',$this->configValidation[$key]);
        foreach($conditions as $condition) {
            if (strcmp('file',$condition)===0) {
                if (!file_exists($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' does not exist.');
                    return false;
                }
                continue;
            }
            if (strcmp('integer',$condition)===0) {
                if (!is_int($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not integer.');
                    return false;
                }
                continue;
            }
            if (strcmp('float',$condition)===0) {
                if (!is_float($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not float.');
                    return false;
                }
                continue;
            }
            if (strcmp('string',$condition)===0) {
                if (!is_string($value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not string.');
                    return false;
                }
                continue;
            }
            if (strcmp('colorcode',$condition)===0) {
                if (!preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $value)) {
                    $this->setConfigValidationError($key, $condition, $value.' is not colorcode.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'min:')) {
                $min = substr($condition, 4);
                if (!is_numeric($min)) {
                    $this->setConfigValidationWarning($key, $condition, 'specified min condition ' . $min .' is not numeric.');
                    continue;
                }
                if ($value < (float) $min) {
                    $this->setConfigValidationError($key, $condition, $value . ' is less than ' . $min . '.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'max:')) {
                $max = substr($condition, 4);
                if (!is_numeric($max)) {
                    $this->setConfigValidationError($key, $condition, 'specified max condition ' . $max . ' is not numeric.');
                    continue;
                }
                if ($value > (float) $max) {
                    $this->setConfigValidationError($key, $condition, $value.' is greater than ' . $max . '.');
                    return false;
                }
                continue;
            }
        }
        return true;
    }

    private function setConfigValidationWarning($key, $rule, $message) {
        if (!array_key_exists($this->configValidationWarning)) $this->configValidationWarning[$key] = [];
        $this->configValidationWarning[$key][$rule] = $message;
        return true;
    }

    private function setConfigValidationError($key, $rule, $message) {
        if (!array_key_exists($this->configValidationError)) $this->configValidationError[$key] = [];
        $this->configValidationError[$key][$rule] = $message;
        return true;
    }

    public function getConfigValidationWarning() {
        return $this->configValidationWarning;
    }

    public function getConfigValidationError() {
        return $this->configValidationError;
    }

    public function configure($config) {
        foreach($this->getValidConfig($config) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    private function setProperties() {
        $this->parsed = $this->ft->parse();
        $this->baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $this->baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        $this->barMaxValue = max($this->parsed['Frequencies']) + 1;
        $this->barMinValue = 0;
        $this->barWidth = $this->canvasWidth * $this->frameXRatio / count($this->parsed['Classes']);
        $this->barHeightPitch = $this->canvasHeight * $this->frameYRatio / $this->barMaxValue;
        $this->gridHeightPitch = 1;
        if ($this->gridHeightPitch < 0.2 * $this->barMaxValue)
            $this->gridHeightPitch = (int) (0.2 * $this->barMaxValue);
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
    }

    public function create() {
        $this->setProperties();
        return $this;
    }

    public function save($filePath) {
        $this->image->save($filePath);
    }
}
