<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->holiday;
    }

    public function getHolidayDate()
    {
        $day = date('Y') . "-{$this->month}-{$this->day}";
        return date('F j', strtotime($day));
    }

    public function getType()
    {
        return $this->type;
    }
}
