<?php

namespace SchoolManagement\Model;

use DateTime;
use SchoolManagement\Entity\AttendanceType;

/**
 * Gera a lista de Presença em CSV
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class AttendanceList
{

    protected $config = [];
    protected $data = [];
    protected $csv;

    /**
     * Cria uma lista de presença a partir da turma, dias e tipos de presença escolhidos.
     * 
     * 
     * @param array $config parâmetro de configuração da lista: nome da turma e tipos de presença
     * @param array $data alunos e a situação de presença em cada dia definido em $config
     */
    public function __construct(array $config, array $data = [])
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
            $this->config['Dates'][] = 'D-' . $dt->format('d/m/Y');
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
