<?php
$previewConfig = [
    'type'                => 'cdn',
    'titleSuffix'         => ' (unpkg)',
    'baseAction'          => './unpkg.php',
    'templateFile'        => 'template.html',
    'noSoundPath'         => '(..)/tmp/nosound.mp3',
    'supportsOldVersions' => false,
    'cdnBaseUrl'          => 'https://unpkg.com/danoniplus',
];
require __DIR__ . '/preview_core.php';