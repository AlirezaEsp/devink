<?php

namespace App\Features\Article\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
