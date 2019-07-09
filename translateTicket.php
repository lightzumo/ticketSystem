<?php

require_once 'php-google-translate-for-free-master/GoogleTranslateForFree.php';

//Single
function translateTicket($sourceLanguage, $targetLanguage, $textToTranslate){

    $source = $sourceLanguage;
    $target = $targetLanguage;
    $attempts = 5;
    $text = $textToTranslate;

    $tr = new GoogleTranslateForFree();
    $result = $tr->translate($source, $target, $text, $attempts);

   return $result;
}


