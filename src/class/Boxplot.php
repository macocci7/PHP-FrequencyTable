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
    private $canvasWidth = 600;
    private $canvasHeight = 500;
    private $canvasBackgroundColor = '#ffffff';
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 1;
    private $gridColor = '#999999';
    private $gridWidth = 1;
    private $gridHeightPitch;
    private $pixGridWidth;
    private $gridMax;
    private $gridMin;
    private $gridVertical = false;
    private $boxCount = 0;
    private $boxWidth = 20;
    private $boxBackgroundColor;
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
    private $jitterDiameter = 2;
    private $mean = false;
    private $meanColor = '#ff0000';
    private $labels;
    private $labelX;
    private $labelY;
    private $caption;
    private $legend = false;
    private $legendCount;
    private $legends;
    private $legendWidth = 100;
    private $legendFontSize = 10;
    private $colors = [
        '#9999cc',
        '#cc9999',
        '#99cc99',
        '#99cccc',
        '#cc6666',
        '#ffcc99',
        '#cccc99',
        '#cc99cc',
    ];

    public function __construct() {
        Image::configure(['driver' => 'imagick']);
    }

    private function setProperties() {
        $this->ft = new FrequencyTable();
        $this->legendCount = count($this->data);
        if (!$this->boxBackgroundColor) $this->boxBackgroundColor = $this->colors;
        $counts = [];
        foreach($this->data as $values) {
            $counts[] = count($values);
        }
        $this->boxCount = max($counts);
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        $maxValues = [];
        foreach($this->data as $data) {
            foreach($data as $key => $values) {
                $maxValues[] = max($values);
            }
        }
        $maxValue = max($maxValues);
        $minValues = [];
        foreach($this->data as $data) {
            foreach($data as $key => $values) {
                $minValues[] = min($values);
            }
        }
        $minValue = min($minValues);
        $this->gridMax = ((int) ($maxValue + ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        $this->gridMin = ((int) ($minValue - ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        $gridHeightSpan = $this->gridMax - $this->gridMin;
        // Note:
        // - The Class Range affects the accuracy of the Mean Value.
        // - This value should be set appropriately: 10% of $gridHeightSpan in this case.
        $clsasRange = ((int) ($gridHeightSpan * 10)) / 100;
        $this->ft->setClassRange($clsasRange);
        $this->pixHeightPitch = $this->canvasHeight * $this->frameYRatio / ($this->gridMax - $this->gridMin);
        // Note:
        // - If $this->gridHeightPitch has a value, that value takes precedence.
        // - The value of $this->girdHeightPitch may be set by the funciton setGridHeightPitch().
        if (!$this->gridHeightPitch) {
            $this->gridHeightPitch = 1;
            if ($this->gridHeightPitch < 0.125 * $gridHeightSpan)
                $this->gridHeightPitch = ( (int) (0.125 * $gridHeightSpan * 10)) / 10;
            if ($this->gridHeightPitch > 0.2 * $gridHeightSpan)
                $this->gridHeightPitch = ( (int) (0.200 * $gridHeightSpan * 10)) / 10;
        }
        $this->pixGridWidth = $this->canvasWidth * $this->frameXRatio / $this->boxCount;
        // Creating an instance of intervention/image.
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the function setLabels().
        if (empty($this->labels)) $this->labels = array_keys(array_fill(0, $this->boxCount, 0));
        return $this;
    }

    public function setData($data) {
        $this->data[0][] = $data;
        return $this;
    }

    public function setDataset($dataset) {
        $this->data = $dataset;
        return $this;
    }

    public function setGridHeightPitch($pitch) {
        if (!is_int($pitch) && !is_float($pitch)) return;
        if ($pitch <= 0) return;
        $this->gridHeightPitch = $pitch;
        return $this;
    }

    public function setSize($width, $height) {
        if (!is_int($width) || !is_int($height)) return;
        if ($width < 100 || $height < 100) return;
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    public function setBoxWidth($width) {
        if (!is_int($width)) return;
        if ($width < $this->boxBorderWidth * 2 + 1) return;
        $this->boxWidth = $width;
        return $this;
    }

    public function setBoxBacground($colors) {
        if (!is_array($colors)) return;
        $this->boxBackgroundColor = $colors;
        return $this;
    }

    public function setLabels($labels) {
        if (!is_array($labels)) return;
        $this->label = [];
        foreach($labels as $label) {
            $this->labels[] = (string) $label;
        }
        return $this;
    }

    public function setLabelX($label) {
        if (!is_string($label)) return;
        $this->labelX = $label;
        return $this;
    }

    public function setLabelY($label) {
        if (!is_string($label)) return;
        $this->labelY = $label;
        return $this;
    }

    public function setCaption($caption) {
        if (!is_string($caption)) return;
        $this->caption = $caption;
        return $this;
    }

    public function setLegends($legends) {
        if (!is_array($legends)) return;
        $this->legends = $legends;
        return $this;
    }

    public function gridVerticalOn() {
        $this->gridVertical = true;
        return $this;
    }

    public function gridVerticalOff() {
        $this->gridVertical = false;
        return $this;
    }

    public function outlierOn() {
        $this->outlier = true;
        return $this;
    }

    public function outlierOff() {
        $this->outlier = false;
        return $this;
    }

    public function jitterOn() {
        $this->jitter = true;
        return $this;
    }

    public function jitterOff() {
        $this->jitter = false;
        return $this;
    }

    public function meanOn() {
        $this->mean = true;
        return $this;
    }

    public function meanOff() {
        $this->mean = false;
        return $this;
    }

    public function legendOn() {
        $this->legend = true;
        return $this;
    }

    public function legendOff() {
        $this->legend = false;
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

    public function setAxis() {
        // Horizontal Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) $this->baseY;
        $x2 = (int) $this->canvasWidth * (3 + $this->frameXRatio) / 4;
        $y2 = (int) $this->baseY;
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        // Vertical Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2;
        $x2 = (int) $this->baseX;
        $y2 = (int) $this->baseY;
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
            $x2 = (int) ($this->canvasWidth * (3 + $this->frameXRatio) / 4);
            $y2 = (int) $y1;
            $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
                $draw->color($this->gridColor);
                $draw->width($this->gridWidth);
            });
        }
        $this->setGridVertical();
        return $this;
    }

    public function setGridVertical() {
        if (!$this->gridVertical) return;
        for ($i = 1; $i <= $this->boxCount; $i++) {
            $x1 = (int) ($this->baseX + $i * $this->pixGridWidth);
            $y1 = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 2);
            $x2 = (int) $x1;
            $y2 = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
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

    public function plotBox($index, $legend) {
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth - 0.5 * $this->boxWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        $y1 = (int) ($this->baseY - ($this->parsed['ThirdQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) ($x1 + $this->boxWidth);
        $y2 = (int) ($this->baseY - ($this->parsed['FirstQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $this->image->rectangle($x1, $y1, $x2, $y2, function ($draw) use($legend) {
            $draw->background($this->boxBackgroundColor[$legend]);
            $draw->border($this->boxBorderWidth, $this->boxBorderColor);
        });
        return $this;
    }

    public function plotMedian($index, $legend) {
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth - 0.5 * $this->boxWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        $y1 = (int) ($this->baseY - ($this->parsed['Median'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) ($x1 + $this->boxWidth);
        $y2 = (int) $y1;
        $this->image->line($x1, $y1, $x2, $y2, function ($draw) {
            $draw->color($this->boxBorderColor);
            $draw->width($this->boxBorderWidth);
        });
        return $this;
    }

    public function plotMean($index, $legend) {
        if (!$this->mean) return;
        $mean = $this->parsed['Mean'];
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x = (int) ($this->baseX + $offsetX);
        $y = (int) $this->baseY - ($mean - $this->gridMin) * $this->pixHeightPitch;
        $this->image->text('+', $x, $y, function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->meanColor);
            $font->align('center');
            $font->valign('center');
        });
    }

    public function plotWhiskerUpper($index, $legend) {
        // upper whisker
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x1 = (int) ($this->baseX + $offsetX);
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
        return $this;
    }

    public function plotWhiskerLower($index, $legend) {
        // lower whisker
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x1 = (int) ($this->baseX + $offsetX);
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
        return $this;
    }

    public function plotWhisker($index, $legend) {
        $this->plotWhiskerUpper($index, $legend);
        $this->plotWhiskerLower($index, $legend);
        return $this;
    }

    public function plotOutliers($index, $legend) {
        if (!$this->outlier) return;
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        foreach($this->getOutliers() as $outlier) {
            $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
            $x = (int) ($this->baseX + $offsetX);
            $y = (int) ($this->baseY - ($outlier - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle($this->outlierDiameter, $x, $y, function ($draw) {
                $draw->background($this->outlierColor);
                $draw->border(1, $this->outlierColor);
            });
        }
        return $this;
    }

    public function plotJitter($index, $legend) {
        if (!$this->jitter) return;
        if (!array_key_exists('data', $this->parsed)) return;
        $data = $this->parsed['data'];
        if (empty($data)) return;
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $baseX = $this->baseX + ($index + ($legend + 0.5) / $legends) * $gridWidth - $this->boxWidth / 2;
        $pitchX = $this->boxWidth / count($data);
        foreach($data as $key => $value) {
            $x = (int) ($baseX + $key * $pitchX);
            $y = (int) ($this->baseY - ($value - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle($this->jitterDiameter, $x, $y, function ($draw) {
                $draw->background($this->jitterColor);
                $draw->border(1, $this->jitterColor);
            });
        }
        return $this;
    }

    public function plotLabels() {
        if (!is_array($this->labels)) return;
        foreach($this->labels as $index => $label) {
            if (!is_string($label) && !is_numeric($label)) continue;
            $x = $this->baseX + ($index + 0.5) * $this->pixGridWidth;
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text((string) $label, $x, $y, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
        return $this;
    }

    public function plotLabelX() {
        $x = (int) $this->canvasWidth / 2;
        $y = $this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3 ;
        $this->image->text((string) $this->labelX, $x, $y, function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->fontColor);
            $font->align('center');
            $font->valign('bottom');
        });
        return $this;
    }

    public function plotLabelY() {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $image = Image::canvas($width, $height, $this->canvasBackgroundColor);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text((string) $this->labelY, $x, $y, function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->fontColor);
            $font->align('center');
            $font->valign('bottom');
        });
        $image->rotate(90);
        $this->image->insert($image, 'left');
        return $this;
    }

    public function plotCaption() {
        $x = $this->canvasWidth / 2;
        $y = $this->canvasHeight * (1 - $this->frameYRatio) / 3;
        $this->image->text((string) $this->caption, $x, $y, function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->fontColor);
            $font->align('center');
            $font->valign('bottom');
        });
    }

    public function plotLegend() {
        if (!$this->legend) return;
        $baseX = $this->canvasWidth * (3 + $this->frameXRatio) / 4 - $this->legendWidth;
        $baseY = 10;
        $x1 = $baseX;
        $y1 = $baseY;
        $x2 = $x1 + $this->legendWidth;
        $y2 = $y1 + $this->legendFontSize * 1.2 * $this->legendCount + 8;
        $this->image->rectangle($x1, $y1, $x2, $y2, function ($draw) {
            $draw->background('#ffffff');
            $draw->border(1, '#000000');
        });
        for ($i = 0; $i < $this->legendCount; $i++) {
            if (empty($this->legends[$i])) {
                $label = 'unknown ' . $i;
            } else {
                $label = $this->legends[$i];
            }
            $x1 = (int) ($baseX + 4);
            $y1 = (int) ($baseY + $i * $this->legendFontSize * 1.2 + 4);
            $x2 = (int) ($x1 + 20);
            $y2 = (int) ($y1 + $this->legendFontSize);
            $this->image->rectangle($x1, $y1, $x2, $y2, function ($draw) use($i) {
                $draw->background($this->boxBackgroundColor[$i]);
                $draw->border(1, $this->boxBorderColor);
            });
            $x = $x2 + 4;
            $y = $y1;
            $this->image->text($label, $x, $y, function ($font) {
                $font->file($this->fontPath);
                $font->size($this->legendFontSize);
                $font->color($this->fontColor);
                $font->align('left');
                $font->valign('top');
            });
        }
    }

    public function plot($index, $legend) {
        // plot a box
        $this->plotBox($index, $legend);
        // plot median
        $this->plotMedian($index, $legend);
        // plot mean
        $this->plotMean($index, $legend);
        // plot a whisker
        $this->plotWhisker($index, $legend);
        // plot outliers
        $this->plotOutliers($index, $legend);
        // plot jitter
        $this->plotJitter($index, $legend);
        return $this;
    }

    public function create() {
        $this->setProperties();
        if (!is_array($this->data)) return false;
        if (empty($this->data)) return false;
        $this->setGrids();
        $this->setGridValues();
        foreach($this->data as $legend => $data) {
            $index = 0;
            foreach($data as $key => $values) {
                $this->ft->setData($values);
                $this->parsed = $this->ft->parse($values);
                $this->plot($index, $legend);
                $index++;
            }
        }
        $this->setAxis();
        $this->plotLabels();
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->plotLegend();
        return $this;
    }

    public function save($filePath) {
        if (!is_string($filePath)) return false;
        if (!(strlen($filePath)>0)) return false;
        $this->image->save($filePath);
        return $this;
    }
}
