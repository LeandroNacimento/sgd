<?php

namespace App\Enums;

enum Role: string
{
    case Administrator = 'administrator';
    case Operator = 'operator';
    case Viewer = 'viewer';
}
