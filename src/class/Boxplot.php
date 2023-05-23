<?php
require_once('../vendor/autoload.php');
require_once('./class/FrequencyTable.php');

use Intervention\Image\ImageManagerStatic as Image;

class Boxplot
{
    private $ft;
    private $image;
    private $data;
    private $parsed = [];
    private $canvasWidth = 400;
    private $canvasHeight = 300;
    private $canvasBackgroundColor = '#ffffff';
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 2;
    private $gridColor = '#999999';
    private $gridWidth = 1;
    private $gridHeightPitch;
    private $pixGridWidth;
    private $gridMax;
    private $gridMin;
    private $boxCount = 0;
    private $boxWidth = 20;
    private $boxBackgroundColor = '#9999cc';
    private $boxBorderColor = '#3333cc';
    private $boxBorderWidth = 1;
    private $pixHeightPitch;
    private $whiskerColor = '#3333cc';
    private $whiskerWidth = 1;
    private $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //private $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    private $fontSize = 16;
    private $fontColor = '#333333';
    private $baseX;
    private $baseY;
    private $outlier = true;
    private $outlierDiameter = 2;
    private $outlierColor = '#ff0000';
    private $jitter = false;
    private $jitterColor = '#009900';
    private $jitterDiameter = 1;

    public function __construct() {
        Image::configure(['driver' => 'imagick']);
    }

    private function setProperties() {
        $this->ft = new FrequencyTable();
        $this->ft->setClassRange(10);
        $this->boxCount = count($this->data);
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 2);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        $maxValues = [];
        foreach($this->data as $key => $values) {
            $maxValues[] = max($values);
        }
        $maxValue = max($maxValues);
        $minValues = [];
        foreach($this->data as $key => $values) {
            $minValues[] = min($values);
        }
        $minValue = min($minValues);
        $this->gridMax = $maxValue + ($maxValue - $minValue) * 0.1;
        $this->gridMin = $minValue - ($maxValue - $minValue) * 0.1;
        $this->pixHeightPitch = $this->canvasHeight * $this->frameYRatio / ($this->gridMax - $this->gridMin);
        $this->gridHeightPitch = 1;
        $gridHeightSpan = $this->gridMax - $this->gridMin;
        if ($this->gridHeightPitch < 0.15 * $gridHeightSpan)
            $this->gridHeightPitch = 0.15 * $gridHeightSpan;
        if ($this->gridHeightPitch > 0.2 * $gridHeightSpan)
            $this->gridHeightPitch = 0.2 * $gridHeightSpan;
        $this->pixGridWidth = $this->canvasWidth * $this->frameXRatio / count($this->data);
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
    }

    public function setData($key, $data) {
        $this->data[$key] = $data;
        return $this;
    }

    public function getUcl() {
        if (!is_array($this->parsed)) return;
        if (!array_key_exists('ThirdQuartile',$this->parsed)) return;
        if (!array_key_exists('InterQuartileRange',$this->parsed)) return;
        return $this->parsed['ThirdQuartile'] + 1.5 * $this->parsed['InterQuartileRange'];
    }
    
    public function getLcl() {
        if (!is_array($this->parsed)) return;
        if (!array_key_exists('FirstQuartile',$this->parsed)) return;
        if (!array_key_exists('InterQuartileRange',$this->parsed)) return;
        return $this->parsed['FirstQuartile'] - 1.5 * $this->parsed['InterQuartileRange'];
    }
    
    public function getOutliers() {
        if (!array_key_exists('data', $this->parsed)) return;
        $ucl = $this->getUcl();
        $lcl = $this->getLcl();
        if (null === $ucl || null === $lcl) return;
        $outliers = [];
        foreach($this->parsed['data'] as $value) {
            if ($value > $ucl || $value < $lcl) $outliers[] = $value;
        }
        return $outliers;
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
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        list($x1,$y1,$x2,$y2) = $this->getVerticalAxisPosition();
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        return $this;
    }

    public function setGrids() {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) $this->baseX;
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch);
            $x2 = (int) ($this->canvasWidth * (1 + $this->frameXRatio) / 2);
            $y2 = (int) $y1;
            $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
        }
        return $this;
    }

    public function setGridValues() {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) ($this->baseX - $this->fontSize * 1.1);
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch + $this->fontSize * 0.4);
            $this->image->text($y, $x1, $y1, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
        return $this;
    }

    public function getBoxPosition($index) {
        return [
            (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth - 0.5 * $this->boxWidth),
            (int) ($this->baseY - ($this->parsed['ThirdQuartile'] - $this->gridMin) * $this->pixHeightPitch),
            (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth + 0.5 * $this->boxWidth),
            (int) ($this->baseY - ($this->parsed['FirstQuartile'] - $this->gridMin) * $this->pixHeightPitch),
        ];
    }

    public function plotBox($index) {
        list($x1, $y1, $x2, $y2) = $this->getBoxPosition($index);
        $this->image->rectangle($x1, $y1, $x2, $y2, function ($draw) {
            $draw->background($this->boxBackgroundColor);
            $draw->border($this->boxBorderWidth, $this->boxBorderColor);
        });
        return $this;
    }

    public function plotMedian($index) {
        $x1 = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth - 0.5 * $this->boxWidth);
        $y1 = (int) ($this->baseY - ($this->parsed['Median'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth + 0.5 * $this->boxWidth);
        $y2 = (int) $y1;
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->boxBorderColor);
            $draw->width($this->boxBorderWidth);
        });
    }

    public function plotWhiskerUpper($index) {
        // upper whisker
        $x1 = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth);
        if ($this->outlier) {
            $max = $this->parsed['Max'];
            $ucl = $this->getUcl();
            $max = ($ucl > $max) ? $max : $ucl;
            $y1 = (int) ($this->baseY - ($max - $this->gridMin) * $this->pixHeightPitch);
        } else {
            $y1 = (int) ($this->baseY - ($this->parsed['Max'] - $this->gridMin) * $this->pixHeightPitch);
        }
        $x2 = (int) $x1;
        $y2 = (int) ($this->baseY - ($this->parsed['ThirdQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->whiskerColor);
            $draw->width($this->whiskerWidth);
        });
        // top bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $y2 = (int) $y1;
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->whiskerColor);
            $draw->width($this->whiskerWidth);
        });
    }

    public function plotWhiskerLower($index) {
        // lower whisker
        $x1 = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth);
        $y1 = (int) ($this->baseY - ($this->parsed['FirstQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) $x1;
        if ($this->outlier) {
            $min = $this->parsed['Min'];
            $lcl = $this->getLcl();
            $min = ($lcl < $min) ? $min : $lcl;
            $y2 = (int) ($this->baseY - ($min - $this->gridMin) * $this->pixHeightPitch);
        } else {
            $y2 = (int) ($this->baseY - ($this->parsed['Min'] - $this->gridMin) * $this->pixHeightPitch);
        }
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->whiskerColor);
            $draw->width($this->whiskerWidth);
        });
        // bottom bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $y1 = (int) $y2;
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->whiskerColor);
            $draw->width($this->whiskerWidth);
        });
    }

    public function plotWhisker($index) {
        $this->plotWhiskerUpper($index);
        $this->plotWhiskerLower($index);
    }

    public function plotOutliers($index) {
        if (!$this->outlier) return;
        foreach($this->getOutliers() as $outlier) {
            $x = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth);
            $y = (int) ($this->baseY - ($outlier - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle($this->outlierDiameter, $x, $y, function ($draw) {
                $draw->background($this->outlierColor);
                $draw->border(1, $this->outlierColor);
            });
        }
    }

    public function plotJitter($index) {
        if (!$this->jitter) return;
        if (!array_key_exists('data', $this->parsed)) return;
        $data = $this->parsed['data'];
        if (empty($data)) return;
        $baseX = $this->baseX + ($index + 0.5) * $this->pixGridWidth - $this->boxWidth / 2;
        $pitchX = $this->boxWidth / count($data);
        foreach($data as $key => $value) {
            $x = (int) ($baseX + $key * $pitchX);
            $y = (int) ($this->baseY - ($value - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle($this->jitterDiameter, $x, $y, function ($draw) {
                $draw->background($this->jitterColor);
                $draw->border(1, $this->jitterColor);
            });
        }
    }

    public function plot($index) {
        // plot a box
        $this->plotBox($index);
        // plot median
        $this->plotMedian($index);
        // plot a whisker
        $this->plotWhisker($index);
        // plot outliers
        $this->plotOutliers($index);
        // plot jitter
        $this->plotJitter($index);
    }

    public function create() {
        $this->setProperties();
        if (!is_array($this->data)) return false;
        if (empty($this->data)) return false;
        $this->setGrids();
        $this->setGridValues();
        $index = 0;
        foreach($this->data as $key => $values) {
            $this->ft->setData($values);
            $this->parsed = $this->ft->parse($values);
            $this->plot($index);
            $index++;
        }
        $this->setAxis();
        return $this;
    }

    public function save($filePath) {
        if (!is_string($filePath)) return false;
        if (!(strlen($filePath)>0)) return false;
        $this->image->save($filePath);
        return $this;
    }
}
