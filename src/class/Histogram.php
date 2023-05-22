<?php
require_once('../vendor/autoload.php');

use Intervention\Image\ImageManagerStatic as Image;

class Histogram {

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
    private $ft;
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

    public function __construct() {
        Image::configure(['driver' => 'imagick']);
    }

    public function getValidConfig($config) {
        if (!is_array($config)) return [];
        if (empty($config)) return [];
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
        $v = new Valitron\Validator([$key, $value]);
        foreach($conditions as $condition) {
            if (strcmp('file',$condition)===0) {
                if (!file_exists($value)) return false;
            }
            if (strcmp('integer',$condition)===0) {
                $v->rule('integer', $key);
                continue;
            }
            if (strcmp('float',$condition)===0) {
                $v->rule('numeric', $key);
                continue;
            }
            if (strcmp('string',$condition)===0) {
                $v->rule('lengthMin', $key, 0);
                continue;
            }
            if (strcmp('colorcode',$condition)===0) {
                $v->rule('regex', $key, '/^#[A-Fa-f0-9]{3,6}$/');
                continue;
            }
            if (str_starts_with($condition, 'min:')) {
                $min = substr($condition, 4);
                if (!is_numeric($min)) continue;
                $v->rule('min', $key, (float) $min);
                echo "min:".$min."\n";
                if ($value < (float) $min) return false;
                continue;
            }
            if (str_starts_with($condition, 'max:')) {
                $max = substr($condition, 4);
                if (!is_numeric($max)) continue;
                $v->rule('max', $key, (float) $max);
                echo "max:".$max."\n";
                if ($value > (float) $max) return false;
                continue;
            }
        }
        return $v->validate();
    }

    public function configure($config) {
        foreach($this->getValidConfig($config) as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getHorizontalAxisPosition() {
        return [
            (int) $this->baseX,
            (int) $this->baseY,
            (int) $this->canvasWidth * (1 + $this->frameXRatio) / 2,
            (int) $this->baseY,
        ];
    }

    public function getVerticalAxisPosition() {
        return [
            (int) $this->baseX,
            (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2,
            (int) $this->baseX,
            (int) $this->baseY,
        ];
    }

    public function setAxis() {
        list($x1,$y1,$x2,$y2) = $this->getHorizontalAxisPosition();
        $this->image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        list($x1,$y1,$x2,$y2) = $this->getVerticalAxisPosition();
        $this->image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
    }

    public function setGrids() {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x1 = $this->baseX;
            $y1 = $this->baseY - $i * $this->barHeightPitch;
            $x2 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y2 = $y1;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
            $x1 = $this->canvasWidth * (1 + $this->frameXRatio) / 2;
            $y1 = $this->baseY - $this->barMaxValue * $this->barHeightPitch;
            $x2 = $x1;
            $y2 = $this->baseY;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
        }
    }

    public function setGridValues() {
        for ($i = $this->barMinValue; $i <= $this->barMaxValue; $i += $this->gridHeightPitch) {
            $x = $this->baseX - $this->fontSize * 1.1;
            $y = $this->baseY - $i * $this->barHeightPitch + $this->fontSize * 0.4;
            $this->image->text($i,$x,$y, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    public function getBarPosition($frequency, $index) {
        return [
            (int) ($this->baseX + $index * $this->barWidth),
            (int) ($this->baseY - $this->barHeightPitch * $frequency),
            (int) ($this->baseX + ($index + 1) * $this->barWidth),
            (int) $this->baseY,
        ];
    }

    public function setBars() {
        if (!array_key_exists('Classes', $this->parsed)) return;
        if (!array_key_exists('Frequencies', $this->parsed))  return;
        $classes = $this->parsed['Classes'];
        $frequencies = $this->parsed['Frequencies'];
        if (empty($classes) || empty($frequencies)) return;
        foreach($classes as $index => $class) {
            list($x1,$y1,$x2,$y2) = $this->getBarPosition($frequencies[$index], $index);
            $this->image->rectangle($x1,$y1,$x2,$y2, function ($draw) {
                $draw->background($this->barBackgroundColor);
                $draw->border($this->barBorderWidth, $this->barBorderColor);
            });
        }
    }

    public function setClasses() {
        if (!array_key_exists('Classes', $this->parsed)) return;
        $classes = $this->parsed['Classes'];
        $x = $this->baseX;
        $y = $this->baseY + $this->fontSize * 1.2;
        $this->image->text($classes[0]['bottom'],$x,$y,function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->classColor);
            $font->align('center');
            $font->valign('bottom');
        });
        foreach($classes as $index => $class) {
            $x = $this->baseX + ($index + 1) * $this->barWidth;
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text($class['top'],$x,$y,function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    public function setFrequencyPolygon() {
        if (!array_key_exists('Frequencies', $this->parsed)) return;
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) return;
        if (count($frequencies) < 2) return;
        for ($i = 0; $i < count($frequencies) - 1; $i++) {
            $x1 = $this->baseX + ($i + 0.5) * $this->barWidth;
            $y1 = $this->baseY - $frequencies[$i] * $this->barHeightPitch;
            $x2 = $this->baseX + ($i + 1.5) * $this->barWidth;
            $y2 = $this->baseY - $frequencies[$i + 1] * $this->barHeightPitch;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->frequencyPolygonColor);
                $draw->width($this->frequencyPolygonWidth);
            });
        }
    }

    public function setCumulativeRelativeFrequencyPolygon() {
        if (!array_key_exists('Frequencies', $this->parsed)) return;
        $frequencies = $this->parsed['Frequencies'];
        if (!is_array($frequencies)) return;
        if (count($frequencies) < 2) return;
        $x1 = $this->baseX;
        $y1 = $this->baseY;
        $yTop = $this->canvasHeight * (1 - $this->frameYRatio) / 2;
        $ySpan = $this->baseY - $yTop;
        foreach($frequencies as $index => $frequency) {
            $crf = $this->ft->getCumulativeRelativeFrequency($frequencies, $index);
            $x2 = $this->baseX + ($index + 1) * $this->barWidth;
            $y2 = $this->baseY - $ySpan * $crf;
            $this->image->line($x1,$y1,$x2,$y2, function ($draw) {
                $draw->color($this->cumulativeRelativeFrequencyPolygonColor);
                $draw->width($this->cumulativeRelativeFrequencyPolygonWidth);
            });
            $x1 = $x2;
            $y1 = $y2;
        }
    }

    public function create($FrequencyTable, $filePath, $option = [
        'bar' => true,
        'frequencyPolygon' => false,
        'cumulativeFrequencyPolygon' => false
    ]) {
        if (!is_string($filePath)) return;
        if (strlen($filePath) == 0) return;
        $this->ft = $FrequencyTable;
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
        $this->setGrids();
        $this->setGridValues();
        if (array_key_exists('bar', $option))
            if ($option['bar']) $this->setBars();
        $this->setAxis();
        if (array_key_exists('frequencyPolygon', $option))
            if ($option['frequencyPolygon']) $this->setFrequencyPolygon();
        if (array_key_exists('cumulativeFrequencyPolygon', $option))
            if ($option['cumulativeFrequencyPolygon']) $this->setCumulativeRelativeFrequencyPolygon();
        $this->setClasses();
        $this->image->save($filePath);
    }
}
