<?php

/*
 * Copyright (C) 2016 Márcio Dias <marciojr91@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SchoolManagement\Model;

/**
 * Description of AttendanceAnalysis
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceAnalysis
{

    protected $students;
    protected $attendances;

    public function __construct($students, $attendances)
    {
        $this->students = $students;
        $this->attendances = $attendances;
    }

    public function getMonthlyAttendance()
    {
        $groupedAttendances = [];

        $this->sortStudents();

        foreach ($this->attendances as $att) {

            $groupedAttendances[$att['enrollmentId']]['name'] = $this->students[$att['enrollmentId']];
            $groupedAttendances[$att['enrollmentId']][$att['date']
                    ->format('Ymd')][$att['attendanceTypeId']] = $att['attendanceType'];
        }

        return $groupedAttendances;
    }

    protected function sortStudents()
    {
        $students = [];
        foreach ($this->students as $st) {
            $students[$st['enrollmentId']] = $st['personFirstName'] . ' ' . $st['personLastName'];
        }

        $this->students = $students;
    }

}
