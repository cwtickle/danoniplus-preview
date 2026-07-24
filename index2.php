<?php
$previewConfig = [
    'type'                => 'cdn',
    'titleSuffix'         => ' (jsdelivr)',
    'indexLinkHref'       => './index2.php',
    'formAction'          => './index2.php',
    'baseAction'          => './index2.php',
    'templateFile'        => 'template2.html',
    'noSoundPath'         => '(..)/tmp/nosound.mp3',
    'supportsOldVersions' => false,
];
require __DIR__ . '/preview_core.php';