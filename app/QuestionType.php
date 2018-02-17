<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionType extends Model
{
    use SoftDeletes;

    const TYPE_SINGLE_ID = 1;
    const TYPE_MULTIPLE_ID = 2;
    const TYPE_SEQUENCE_ID = 3;
    const TYPE_STRING_ID = 4;
    const TYPE_TEXT_ID = 5;
}
