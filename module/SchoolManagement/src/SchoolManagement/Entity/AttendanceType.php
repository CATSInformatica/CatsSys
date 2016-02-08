<?php

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="attendance_type")
 * @ORM\Entity(readOnly=true)
 */
class AttendanceType
{

    const TYPE_ATTENDANCE_BEGIN = 1;
    const TYPE_ATTENDANCE_END = 2;
    const TYPE_ATTENDANCE_ALLOWANCE_BEGIN = 3;
    const TYPE_ATTENDANCE_ALLOWANCE_END = 4;

    /**
     *
     * @var integer
     * @ORM\Column(name="attendance_type_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $attendanceTypeId;

    /**
     *
     * @var string
     * @ORM\Column(name="attendance_type", type="string", length=50, nullable=false)
     */
    protected $attendanceType;

    /**
     * 
     * @return integer
     */
    public function getAttendanceTypeId()
    {
        return $this->attendanceTypeId;
    }

    /**
     * 
     * @return string
     */
    public function getAttendanceType()
    {
        return $this->attendanceType;
    }

}
