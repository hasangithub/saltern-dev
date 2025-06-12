<?php

namespace App\Enums;

enum RelationshipType: string
{
    case HUSBAND = 'Husband';
    case WIFE = 'Wife';
    case SON = 'Son';
    case DAUGHTER = 'Daughter';
    case FATHER = 'Father';
    case MOTHER = 'Mother';
    case BROTHER = 'Brother';
    case SISTER = 'Sister';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
