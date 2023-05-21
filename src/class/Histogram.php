<?php
require_once('../vendor/autoload.php');

use Intervention\Image\ImageManagerStatic as Image;

class Histogram {

    private $canvasWidth = 600;
    private $canvasHeight = 400;
    private $canvasBackgroundColor = '#ffffff';
    private $barWidth;
    private $barHeightPitch;
    private $barBackgroundColor = '#0000ff';
    private $barBorderColor = '#9999ff';
    private $barBorderWidth = 2;
    private $frameXRatio = 0.8;
    private $frameYRatio = 0.7;
    private $axisColor = '#666666';
    private $axisWidth = 2;
    private $classColor = '333333';
    private $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //private $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    private $fontSize = 16;

    public function __construct() {
        Image::configure(['driver' => 'imagick']);
    }

    public function getHorizontalAxisPosition() {
        $baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        return [
            (int) $baseX,
            (int) $baseY,
            (int) $this->canvasWidth * (1 + $this->frameXRatio) / 2,
            (int) $baseY,
        ];
    }

    public function getVerticalAxisPosition() {
        $baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        return [
            (int) $baseX,
            (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2,
            (int) $baseX,
            (int) $baseY,
        ];
    }

    public function setAxis($image) {
        list($x1,$y1,$x2,$y2) = $this->getHorizontalAxisPosition();
        $image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
        list($x1,$y1,$x2,$y2) = $this->getVerticalAxisPosition();
        $image->line($x1,$y1,$x2,$y2,function ($draw) {
            $draw->color($this->axisColor);
            $draw->width($this->axisWidth);
        });
    }

    public function getBarPosition($frequency, $index) {
        $baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        return [
            (int) ($baseX + $index * $this->barWidth),
            (int) ($baseY - $this->barHeightPitch * $frequency),
            (int) ($baseX + ($index + 1) * $this->barWidth),
            (int) $baseY,
        ];
    }

    public function setBars($image, $parsed) {
        $classes = $parsed['Classes'];
        $frequencies = $parsed['Frequencies'];
        $this->barWidth = $this->canvasWidth * $this->frameXRatio / count($classes);
        $this->barHeightPitch = $this->canvasHeight * $this->frameYRatio / max($frequencies);
        foreach($classes as $index => $class) {
            list($x1,$y1,$x2,$y2) = $this->getBarPosition($frequencies[$index], $index);
            $image->rectangle($x1,$y1,$x2,$y2, function ($draw) {
                $draw->background($this->barBackgroundColor);
                $draw->border($this->barBorderWidth, $this->barBorderColor);
            });
        }
    }

    public function setClasses($image, $classes) {
        $baseX = $this->canvasWidth * (1 - $this->frameXRatio) / 2;
        $baseY = $this->canvasHeight * (1 + $this->frameYRatio) / 2;
        $x = $baseX;
        $y = $baseY + $this->fontSize * 1.2;
        $image->text($classes[0]['bottom'],$x,$y,function ($font) {
            $font->file($this->fontPath);
            $font->size($this->fontSize);
            $font->color($this->classColor);
            $font->align('center');
            $font->valign('bottom');
        });
        foreach($classes as $index => $class) {
            $x = $baseX + ($index + 1) * $this->barWidth;
            $y = $baseY + $this->fontSize * 1.2;
            $image->text($class['top'],$x,$y,function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->classColor);
                $font->align('center');
                $font->valign('bottom');
            });
        }
    }

    public function create($FrequencyTable) {
        $parsed = $FrequencyTable->parse();
        $bgColor = '#fff';
        $fileName = 'img/histogram.png';
        $image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
        $this->setBars($image, $parsed);
        $this->setAxis($image);
        $this->setClasses($image, $parsed['Classes']);
        $image->save($fileName);
    }
}
