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

namespace AdministrativeStructure\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Authorization\Entity\Role;

/**
 * Abstração da tabela `job`.
 * 
 * Classe Cargo. Permite a manipulação de cargos e sua integração papéis de usuário no sistema e departamentos
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 * @ORM\Table(name="job")
 * @ORM\Entity(repositoryClass="AdministrativeStructure\Entity\Repository\JobRepository")
 */
class Job
{

    /**
     * Abstração da coluna `job_id`.
     *
     * @var integer
     * @ORM\Column(name="job_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * 
     */
    protected $jobId;

    /**
     * Abstração da coluna `job_name`.
     *
     * @var string
     * @ORM\Column(name="job_name", type="string", length=100, nullable=false, unique=true)
     */
    protected $jobName;

    /**
     * Abstração da coluna `job_description`.
     * 
     * Contém a descrição do cargo.
     * Ex: *"Responsável pelos simulados: formatação, aplicação e correção."*
     * 
     * @var string
     * @ORM\Column(name="job_description", type="text", nullable=false)
     */
    protected $jobDescription;

    /**
     * Abstração da coluna `department`.
     * 
     * Departamento em que o cargo está alocado.
     * 
     * @var Department
     * @ORM\ManyToOne(targetEntity="Department", inversedBy="jobs")
     * @ORM\JoinColumn(name="department", referencedColumnName="department_id")
     */
    protected $department;

    /**
     * Papel correspondente no sistema. Abstração da coluna `role`.
     * 
     * Caso um cargo possua um papel correspondente no sistema, sempre que um usuário for associado a ele, 
     * o usuário ganha o papel associado ao cargo, liberando recursos específicos do sistema.
     * 
     * @var Role
     * 
     * @ORM\OneToOne(targetEntity="Authorization\Entity\Role", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="role", referencedColumnName="role_id", nullable=false)
     */
    protected $role;

    /**
     * Cargo imediatamente superior. Abstração da coluna 'parent'.
     * 
     * Autorelacionamento `ManyToOne` bidirecional com $children
     * 
     * @var Job
     * @ORM\ManyToOne(targetEntity="Job", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="job_id")
     */
    protected $parent;

    /**
     *
     * @var Collection
     * @ORM\OneToMany(targetEntity="Office", mappedBy="job")
     */
    protected $offices;

    /**
     * Indica se o cargo está disponível para uso
     * @var bool
     * @ORM\Column(name="job_is_available", type="boolean", nullable=false)
     */
    protected $isAvailable;

    /**
     * Cargos imediatamente subordinados.
     * 
     * Autorelacionamento `OneToMany` bidirecional com $parent
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="parent")
     */
    protected $children;

    /**
     * Data de criação do cargo.
     * 
     * @var \DateTime
     * @ORM\Column(name="job_creation_date", type="datetime", nullable=false)
     */
    protected $creationDate;

    /**
     * Última revisão do cargo.
     * 
     * @var \DateTime
     * @ORM\Column(name="job_last_revision_date", type="datetime", nullable=true)
     */
    protected $lastRevisionDate;

    /**
     * Armazena o cargo imeditamente superior anterior (necessário para edição)
     * 
     * 
     * @var Job
     */
    protected $parentBuffer;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->creationDate = new \DateTime();
    }

    /**
     * Cria um papel para o cargo.
     * Cria uma instância de Authorization\Entity\Role com o nome time(). Cada cargo deve possuir um papel. Todos os 
     * papéis criados a partir de cargos são definidas por time().
     */
    protected function initRole()
    {
        if (!isset($this->role)) {
            $this->role = new Role();
            $this->role->setRoleName(time());
        }
    }

    /**
     * Retorna o id de cadastro do cargo.
     * 
     * @return integer
     */
    public function getJobId()
    {
        return $this->jobId;
    }

    /**
     * Retorna o nome dado ao cargo.
     * 
     * @return string
     */
    public function getJobName()
    {
        return $this->jobName;
    }

    /**
     * Returna a descrição do cargo.
     * 
     * @return string
     */
    public function getJobDescription()
    {
        return $this->jobDescription;
    }

    /**
     * Retorna o departamento ao qual o cargo está alocado.
     * 
     * @return Department
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Retorna o papel do cargo no sistema.
     * 
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Define o nome do cargo.
     * 
     * @param string $jobName
     * @return Self
     */
    public function setJobName($jobName)
    {
        $this->jobName = $jobName;
        return $this;
    }

    /**
     * Define a descrição do cargo.
     * 
     * @param type $jobDescription
     * @return Self
     */
    public function setJobDescription($jobDescription)
    {
        $this->jobDescription = $jobDescription;
        return $this;
    }

    /**
     * Retorna o departamento ao qual o cargo está alocado.
     * 
     * @param \AdministrativeStructure\Entity\Department $department
     * @return Self
     */
    public function setDepartment(Department $department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * Retorna a situação do cargo.
     * 
     * Se ($isAvailable === false) o cargo está desabilitado e não pode ser associado a nenhuma pessoa. Caso contrário,
     * o cargo está disponível.
     * 
     * @return bool
     */
    public function getIsAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * 
     * @param bool $value
     * @return Self
     */
    public function setIsAvailable($value)
    {
        $this->isAvailable = $value;
        return $this;
    }

    /**
     * Returna o cargo imediatamente superior
     * 
     * @return Job
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Retorna um vetor com os cargos imediatamente subordinados.
     * 
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Define o cargo imediatamente superior e o papel herdado.
     * 
     * Se já existe um cargo pai retira o papel correspondente ao cargo filho. Se não existe, adiciona o papel 
     * correspondente ao cargo filho. A referência do pai anterior deve ser guardada para ser salva no banco.
     * 
     * @param \AdministrativeStructure\Entity\Job $parent
     * @return Self
     */
    public function setParent(Job $parent)
    {
        $this->initRole();

        if (isset($this->parent) && $parent->getJobId() !== $this->parent->getJobId()) {
            $this->parentBuffer = $this->parent;
            $this->parentBuffer->removeChildren(new ArrayCollection([$this]));
        }

        $parent->addChildren(new ArrayCollection([$this]));
        $this->parent = $parent;
        return $this;
    }

    /**
     * Método utilizado na ação de remoção
     * 
     * @param Job $parent
     * @return Self
     */
    public function addNewParent(Job $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Adiciona os cargos imediatamente subordinados.
     * 
     * Insere novos cargos filhos e herda seu papel
     * 
     * @param Collection $children
     */
    public function addChildren(Collection $children)
    {
        $roles = new ArrayCollection();
        foreach ($children as $child) {
            if (!$this->hasChild($child)) {
                $this->children->add($child);
                $roles->add($child->getRole());
            }
        }
        $this->role->addParents($roles);
        return $this;
    }

    /**
     * Torna o cargo orfão
     */
    public function removeParent()
    {
        $this->parent = null;
    }

    /**
     * Remove os cargos filhos especificados e remove os papéis a eles associados.
     * 
     * Remove os cargos filhos e suas heranças
     * 
     * @param Collection $children
     */
    public function removeChildren(Collection $children)
    {
        $roles = new ArrayCollection();
        foreach ($children as $child) {
            $this->children->removeElement($child);
            $roles->add($child->getRole());
        }
        $this->role->removeParents($roles);
        return $this;
    }

    /**
     * Verifica se o departamento possui o departamento imediatamente subordinado $child.
     * 
     * @param Job $child
     * @return bool
     */
    public function hasChild(Job $child)
    {
        return $this->children->contains($child);
    }

    /**
     * Apesar de não intuitivo este método define quem serão os ancestrais de $role.
     *
     * Durante a criação de um novo cargo um novo papel é criado e armazenado em $role. Este método insere todas os
     * papéis dos quais o papel criado será herdeiro (herdará previlégios).
     * 
     * @param Collection $roles
     * @return Self
     */
    public function setParentRoles(Collection $roles)
    {
        $this->initRole();
        $this->role->addParents($roles);
        return $this;
    }

    /**
     * Apesar de não intuitivo este método define quem serão os ancestrais de $role.
     *
     * Durante a edição de um novo cargo este método insere todas os papéis dos quais o papel correspondente ao cargo
     * será herdeiro (herdará previlégios). Se o papel já possui herança com um dos papéis do vetor ele é ignorado.
     * 
     * @param Collection $roles
     * @return Self
     */
    public function addParentRoles(Collection $roles)
    {
        $currentRoles = $this->role->getParents();
        $remove = new ArrayCollection();
        $add = new ArrayCollection();

        foreach ($roles as $r) {
            if (!$currentRoles->contains($r)) {
                $add->add($r);
            }
        }

        foreach ($currentRoles as $r) {
            if (!$roles->contains($r)) {
                $remove->add($r);
            }
        }

        $this->role->removeParents($remove);
        $this->role->addParents($add);

        return $this;
    }

    /**
     * Retorna o cargo superior anterior
     * @return type
     * @return Job
     */
    public function getParentBuffer()
    {
        return $this->parentBuffer;
    }

    public function getRoleIds()
    {
        $rids = [];
        if (isset($this->role)) {
            $parentRoles = $this->role->getParents()->toArray();
            foreach ($parentRoles as $r) {
                if (!is_numeric($r->getRoleName())) {
                    $rids[] = $r->getRoleId();
                }
            }
        }
        return $rids;
    }

    /**
     * Adiciona novos cargos ao trabalho. Se o cargo já existe ele é ignorado.
     * 
     * @param Collection $offices
     * @return AdministrativeStructure\Entity\Job
     */
    public function addOffices(Collection $offices)
    {
        foreach ($offices as $office) {
            if (!$this->hasOffice($office)) {
                $this->offices->add($office);
            }
        }
        return $this;
    }

    /**
     * Verifica se o cargo já existe.
     * 
     * @param AdministrativeStructure\Entity\Office $office
     * @return bool
     */
    public function hasOffice(Office $office)
    {
        return $this->offices->contains($office);
    }

    /**
     * Retorna a data de criação do cargo.
     * 
     * @param string $format formato da data a ser retornada
     * @return mixed string|null data formatada de acordo com $format
     */
    public function getCreationDate($format = 'd/m/Y \à\s H:m:i')
    {
        if ($this->creationDate !== null) {
            return $this->creationDate->format($format);
        } else {
            return null;
        }
    }

    /**
     * Retorna a última data de revisão do cargo.
     * 
     * @param string $format formato da data a ser retornada
     * @return mixed string|null data formatada de acordo com $format
     */
    public function getLastRevisionDate($format = 'd/m/Y \à\s H:m:i')
    {
        if ($this->lastRevisionDate !== null) {
            return $this->lastRevisionDate->format($format);
        } else {
            return null;
        }
    }

    /**
     * Define a data da última revisão realizada no cargo.
     * 
     * @param \DateTime $lastRevisionDate
     * @return Self
     */
    public function setLastRevisionDate(\DateTime $lastRevisionDate)
    {
        $this->lastRevisionDate = $lastRevisionDate;
        return $this;
    }

}
