<?php

class ClassroomService
{
    public function create(array $data): Classroom
    {
        $classroom = Classroom::create($data);
        // tambahan logic lain…
        return $classroom;
    }
}
