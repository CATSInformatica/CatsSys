<?php

namespace SchoolManagement\Model;

use DateTime;
use SchoolManagement\Entity\AttendanceType;

/**
 * Description of Attendance
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceList
{

    protected $config = [];
    protected $data = [];
    protected $csv;

    public function __construct($config, $data = [])
    {
        $this->setConfig($config);
        $this->setData($data);
    }

    public function setConfig($config)
    {
        $this->config['SchoolClass'] = $config['schoolClasses'];
        $this->config['AttendanceTypes'] = $config['attendanceType'];
        $this->config['AttendanceCounter'] = count($config['attendanceType']);

        foreach ($config['dates'] as $value) {
            $dt = new DateTime($value['attendanceDate']);
            $this->config['Dates'][] = $dt->format('d/m/Y');
        }
    }

    public function setData($data)
    {
        foreach ($data as $sd) {
            $this->data[] = [$sd['enrollmentId'], $sd['personFirstName'] . ' ' . $sd['personLastName']];
        }
    }

    protected function generateCsv()
    {
        $this->csv[] = array('SchoolClass', $this->config['SchoolClass']);
        $this->csv[] = array_merge(['AttendanceTypes'], $this->config['AttendanceTypes']);
        $this->csv[] = array('AttendanceNumber', $this->config['AttendanceCounter']);
        $this->csv[] = array_merge(['Dates'], $this->config['Dates']);
        $this->csv[] = array("", "");
        $this->csv[] = array("", "");


        $dataHeader = array("ENROLLMENT_ID", "NAME");

        foreach ($this->config['Dates'] as $date) {
            foreach ($this->config['AttendanceTypes'] as $attType) {

                $type = "";

                switch ($attType) {
                    case AttendanceType::TYPE_ATTENDANCE_BEGIN:
                        $type = 'FREQ. INÍCIO';
                        break;
                    case AttendanceType::TYPE_ATTENDANCE_END:
                        $type = 'FREQ. FIM';
                        break;
                    case AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_BEGIN:
                        $type = 'ABONO INÍCIO';
                        break;
                    case AttendanceType::TYPE_ATTENDANCE_ALLOWANCE_END:
                        $type = 'ABONO FIM';
                        break;
                }

                $dataHeader[] = $type . ' - ' . $date;
            }
            $dataHeader[] = "--#######--";
        }

        $this->csv[] = $dataHeader;
        $this->csv = array_merge($this->csv, $this->data);
    }

    public function getCsv()
    {
        if (empty($this->csv)) {
            $this->generateCsv();
        }

        return $this->csv;
    }

}
