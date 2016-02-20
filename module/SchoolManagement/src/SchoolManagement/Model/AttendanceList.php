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
        $this->config['SchoolClassName'] = $config['className'];
        $this->config['SchoolClassId'] = $config['schoolClasses'];

        foreach ($config['attendanceType'] as $attType) {
            $this->config['AttendanceTypes'][$attType] = AttendanceType::getAttendanceTypeName($attType);
        }

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
        $this->csv[] = array('SchoolClassId', $this->config['SchoolClassId'], $this->config['SchoolClassName']);

        $attTypesIds = ['AttendanceTypesNames'];
        $attTypesNames = ['AttendanceTypesNames'];
        foreach ($this->config['AttendanceTypes'] as $idx => $attType) {
            $attTypesIds[] = $idx;
            $attTypesNames[] = $attType;
        }

        $this->csv[] = $attTypesIds;
        $this->csv[] = $attTypesNames;
        $this->csv[] = array_merge(['Dates'], $this->config['Dates']);
        $this->csv[] = array("", "");
        $this->csv[] = array("", "");

        $dataHeader = array("ENROLLMENT_ID", "NAME");

        foreach ($this->config['Dates'] as $date) {
            foreach ($this->config['AttendanceTypes'] as $attType) {
                $dataHeader[] = $attType . ' - ' . $date;
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
