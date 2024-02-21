<?php

namespace Macocci7;

use Macocci7\PHPFrequencyTable\FrequencyTable;

class Outlier
{
    public function getUcl(array $parsed)
    {
        if (!array_key_exists('ThirdQuartile', $parsed)) {
            return;
        }
        if (!array_key_exists('InterQuartileRange', $parsed)) {
            return;
        }
        return $parsed['ThirdQuartile'] + 1.5 * $parsed['InterQuartileRange'];
    }

    public function getLcl(array $parsed)
    {
        if (!array_key_exists('FirstQuartile', $parsed)) {
            return;
        }
        if (!array_key_exists('InterQuartileRange', $parsed)) {
            return;
        }
        return $parsed['FirstQuartile'] - 1.5 * $parsed['InterQuartileRange'];
    }

    public function getOutliers($data)
    {
        $ft = new FrequencyTable();
        if (!$ft->isSettableData($data)) {
            return;
        }

        $ft->setClassRange(10);
        $ft->setData($data);
        $parsed = $ft->parse();

        $ucl = $this->getUcl($parsed);
        $lcl = $this->getLcl($parsed);
        if (!$ucl || !$lcl) {
            return;
        }

        $outliers = [];
        foreach ($data as $value) {
            if ($value > $ucl || $value < $lcl) {
                $outliers[] = $value;
            }
        }
        unset($ft);
        return $outliers;
    }
}
