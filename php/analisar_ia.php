<?php

require __DIR__ . '/../vendor/autoload.php'; // remove this line if you use a PHP Framework.

use Orhanerday\OpenAi\OpenAi;

if(isset($_GET["prompt"])){

    $prompt = $_GET["prompt"];

    $open_ai_key = getenv('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');

    $complete = $open_ai->completion([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => $prompt,
        'temperature' => 0.9,
        'max_tokens' => 150,
        'frequency_penalty' => 0,
        'presence_penalty' => 0.6,
    ]);

    if ($complete =! null) {
        $php_obj = json_decode($complete);
        $response = $php_obj->choices[0]->text;
    }
    }
?>