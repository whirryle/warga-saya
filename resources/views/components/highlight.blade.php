@props(['text' => '', 'query' => ''])

@php
    $query = trim((string) $query);
    $text = (string) $text;

    if ($query === '' || $text === '') {
        echo e($text);
        return;
    }

    $lowerText = mb_strtolower($text);
    $lowerQuery = mb_strtolower($query);
    $queryLength = mb_strlen($lowerQuery);
    $offset = 0;
    $parts = [];

    while (($pos = mb_strpos($lowerText, $lowerQuery, $offset)) !== false) {
        if ($pos > $offset) {
            $parts[] = ['text' => mb_substr($text, $offset, $pos - $offset), 'match' => false];
        }
        $parts[] = ['text' => mb_substr($text, $pos, $queryLength), 'match' => true];
        $offset = $pos + $queryLength;
    }

    if ($offset < mb_strlen($text)) {
        $parts[] = ['text' => mb_substr($text, $offset), 'match' => false];
    }

    foreach ($parts as $part) {
        if ($part['match']) {
            echo '<mark style="background-color: rgba(253, 224, 71, 0.85); color: #000; padding: 0 2px; border-radius: 2px;">' . e($part['text']) . '</mark>';
        } else {
            echo e($part['text']);
        }
    }
@endphp
