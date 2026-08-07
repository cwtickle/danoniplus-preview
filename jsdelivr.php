<?php
$previewConfig = [
    'type'                => 'cdn',
    'titleSuffix'         => ' (jsdelivr)',
    'baseAction'          => './jsdelivr.php',
    'templateFile'        => 'template.html',
    'noSoundPath'         => '(..)/tmp/nosound.mp3',
    'supportsOldVersions' => false,
    'cdnBaseUrl'          => 'https://cdn.jsdelivr.net/npm/danoniplus',
    'sourceKey'           => 'jsdelivr',
];
require __DIR__ . '/preview_core.php';