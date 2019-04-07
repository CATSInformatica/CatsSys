<?php

namespace SchoolManagement\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="attendance", uniqueConstraints={
 *      @ORM\UniqueConstraint(name="attendance_unique_idx",
 *          columns={"enrollment_id", "attendance_type_id", "attendance_date"})
 * })
 * @ORM\Entity(repositoryClass="SchoolManagement\Entity\Repository\AttendanceRepository")
 */
class Attendance
{

    /**
     *
     * @var integer
     * @ORM\Column(name="attendance_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $attendanceId;

    /**
     *
     * ManyToOne Bidirectional
     *
     * @var Enrollment
     * @ORM\ManyToOne(targetEntity="Enrollment", inversedBy="attendances")
     * @ORM\JoinColumn(name="enrollment_id", referencedColumnName="enrollment_id", nullable=false)
     */
    protected $enrollment;

    /**
     *
     * ManyToOne Unidirectional
     *
     * @var AttendanceType
     * @ORM\ManyToOne(targetEntity="AttendanceType")
     * @ORM\JoinColumn(name="attendance_type_id", referencedColumnName="attendance_type_id", nullable=false)
     */
    protected $attendanceType;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="attendance_date", type="date", nullable=false);
     */
    protected $date;

    public function setEnrollment(Enrollment $enroll)
    {
        $enroll->addAttendance($this);
        $this->enrollment = $enroll;
    }

    public function setAttendanceType(AttendanceType $attType)
    {
        $this->attendanceType = $attType;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

}
