<?php
require_once __DIR__ . '/ml/SentimentService.php';

$svc = new SentimentService();

$examples = [
    "I really enjoy this class",
    "The subject is very confusing and difficult",
    "The class is okay only",
];

foreach ($examples as $txt) {
    echo htmlspecialchars($txt) . " => " . $svc->classifyText($txt) . "<br>";
}
