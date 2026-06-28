<?php

namespace App\Enums;

enum DocumentPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
