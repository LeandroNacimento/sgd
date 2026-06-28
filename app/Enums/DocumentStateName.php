<?php

namespace App\Enums;

enum DocumentStateName: string
{
    case Draft = 'Draft';
    case InReview = 'In Review';
    case Published = 'Published';
    case Archived = 'Archived';
}
