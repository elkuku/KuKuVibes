<?php

namespace App\DTT;

class Feed
{
    public function __construct(
        public string $title,
        public string $xmlUrl,
        public string $htmlUrl
    )
    {
    }
}
