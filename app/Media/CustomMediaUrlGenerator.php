<?php

namespace App\Media;

use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;
use Illuminate\Support\Facades\URL;

class CustomMediaUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        return URL::to('/') . '/storage' . '/' . $this->getPathRelativeToRoot();
    }
}
