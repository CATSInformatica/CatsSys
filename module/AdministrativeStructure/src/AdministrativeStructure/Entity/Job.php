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
 * @ORM\Entity
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
     * @ORM\Column(name="job_description", type="string", length=500)
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
     * Cargos imediatamente subordinados.
     * 
     * Autorelacionamento `OneToMany` bidirecional com $parent
     * 
     * @var Collection
     * @ORM\OneToMany(targetEntity="Job", mappedBy="parent")
     */
    protected $children;

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
    }

    /**
     * Cria um papel para o cargo.
     * Cria uma instância de Authorization\Entity\Role com o nome time(). Cada cargo deve possuir um papel. Todos os 
     * papéis criados a partir de cargos são definidas por time(). Se $role já está definido então remove os papéis
     * de base (não numéricos).
     */
    protected function initRole()
    {
        if (!isset($this->role)) {
            $this->role = new Role();
            $this->role->setRoleName(time());
        } else {
            $this->role->removeNonNumericalParentRoles();
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

        if (isset($this->parent)) {
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
     * papéis dos quais o papel criado será herdeiro (herdará previlégios). A herança é composta de papéis básicos e 
     * papéis definidos por outros cargos.
     * 
     * @param Collection $roles
     * @return Self
     */
    public function addParentRoles(Collection $roles)
    {
        $this->initRole();

        $this->role->addParents($roles);

        return $this;
    }

}
