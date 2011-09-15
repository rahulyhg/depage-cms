<?php

namespace depage\graphics;

class graphics_graphicsmagick extends graphics_imagemagick {
    protected function crop($width, $height, $x = 0, $y = 0) {
        // '+' for positive offset (the '-' is already there)
        $x = ($x < 0) ? $x : '+' . $x;
        $y = ($y < 0) ? $y : '+' . $y;

        $xExtent = ($x > 0) ? "+0" : $x;
        $yExtent = ($y > 0) ? "+0" : $y;
        $this->command .= " -gravity NorthWest -crop {$width}x{$height}{$x}{$y}\! -gravity NorthWest -extent {$width}x{$height}{$xExtent}{$yExtent}";
        $this->size = array($width, $height);
    }

    public function render($input, $output = null) {
        $this->input        = $input;
        $this->imageSize    = getimagesize($this->input);
        $this->output       = ($output == null) ? $input : $output;

        $this->outputFormat = $this->obtainFormat($this->output);

        $this->command = $this->executable . " convert {$this->input} -background none";

        $this->processQueue();

        if ($this->background === 'checkerboard') {
            $tempFile = tempnam(sys_get_temp_dir(), 'depage-graphics');
            $this->command .= " miff:{$tempFile}";

            $this->execCommand();

            $canvasSize = $this->size[0] . "x" . $this->size[1];

            $this->command = $this->executable . " convert";
            $this->command .= " -page {$canvasSize} -size {$canvasSize} pattern:checkerboard";
            $this->command .= " -page {$canvasSize} miff:{$tempFile} -flatten +page {$this->output}";

            $this->execCommand();
            unlink($tempFile);
        } else {
            $this->command .= $this->background() . ' ' . $this->output;

            $this->execCommand();
        }
    }

    protected function background() {
        if ($this->background[0] === '#') {
            // TODO escape!!
            $background = " -flatten -background \"{$this->background}\"";
        } else if ($this->outputFormat == 'jpg') {
            $background = " -flatten -background \"#FFF\"";
        } else {
            $background = '';
        }

        return $background;
    }
}