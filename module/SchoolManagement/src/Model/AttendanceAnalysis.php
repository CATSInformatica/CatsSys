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
    protected $groupedAttendances;

    public function __construct($students, $attendances)
    {
        $this->students = $students;
        $this->attendances = $attendances;
        $this->groupedAttendances = [];
    }

    public function getMonthlyAttendance()
    {
        $this->sortStudents();
        $this->sortAttendances();

        return $this->groupedAttendances;
    }

    protected function sortStudents()
    {
        foreach ($this->students as $st) {
            $this->groupedAttendances[$st['enrollmentId']]['name'] = $st['personFirstName'] . ' ' .
                $st['personLastName'];
        }
    }

    public function sortAttendances()
    {
        foreach ($this->attendances as $att) {
            $this->groupedAttendances[$att['enrollmentId']][$att['date']
                    ->format('Ymd')][$att['attendanceTypeId']] = $att['attendanceType'];
        }
    }

}
