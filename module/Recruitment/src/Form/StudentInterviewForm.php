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

namespace Recruitment\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Recruitment\Form\Fieldset\StudentInterviewFieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;

/**
 * Formulário de entrevista para candidatos de processos seletivos de alunos.
 *
 * @author Márcio Dias <marciojr91@gmail.com>
 */
class StudentInterviewForm extends Form implements InputFilterProviderInterface
{

    public function __construct(ObjectManager $obj)
    {
        parent::__construct('student-interview');

        $this->setHydrator(new DoctrineHydrator($obj));

        $interviewFieldset = new StudentInterviewFieldset($obj);
        $interviewFieldset->setUseAsBaseFieldset(true);
        $this->add($interviewFieldset);

        $this->add([
                'name' => 'descIntro',
                'type' => 'multiCheckbox',
                'options' => [
                    'label' => 'Para iniciar a entrevista, e manter o clima leve,
                neste momento iniciaremos as apresentações.',
                    'value_options' => [
                        'Apresentem-se (falem sobre vocês, o que e onde estudam e atividades que exercem no CATS).',
                        'Pergunte ao candidato como ele conheceu o CATS e explique sobre o funcionamento de modo geral (horários de aulas, monitorias, simulados, banca de redações).',
                        'Fale para o candidato que a entrevista será somente uma conversa e que ele deve ser sincero em todas as respostas.'
                    ]
                ]
            ])
            ->add([
                'name' => 'descSocioVul',
                'type' => 'multiCheckbox',
                'options' => [
                    'label' => 'De acordo com as respostas da pré-entrevista, '
                    . 'peça para que o candidato comente o itens abaixo e, '
                    . 'qualquer momento de dúvida em alguma resposta, você '
                    . '(entrevistador) fique à vontade para questioná-lo. '
                    . 'Aproveite para esclarecer possíveis questões em aberto.',
                    'value_options' => [],
                ]
            ])
            ->add([
                'name' => 'descStudent1',
                'type' => 'multiCheckbox',
                'options' => [
                    'label' => 'SOBRE O CATS',
                    'value_options' => [
                        'Por que quer ser aluno do CATS?',
                        // 'Quais são seus objetivos?',
                        // 'Por que quer ingressar no ensino superior?',
                        // 'Já possui algum curso ou área em mente?',
                        // 'O que você acha que precisa fazer para conseguir alcançar os seus objetivos (com a rotina que possui)?',
                        // 'Caso o candidato trabalhe ou esteja no 3º ano do ensino médio verificar se ele terá tempo e disposição para estudar',
                        // 'Vai conseguir dar conta? Como?',
                    ],
                ]
            ])
            ->add([
                'name' => 'descStudent2',
                'type' => 'multiCheckbox',
                'options' => [
                    'label' => 'Sobre as nossas atividades:',
                    'value_options' => [
                        'Você frequentará as aulas de sábado?',
                        'Você frequentará as monitorias?',
                        'Você frequentará os simulados?',
                        'Levando em consideração sua rotina e as atividades do CATS, acha que vai conseguir
conciliar? (Enfatizar para candidatos que trabalham ou que estejam no 3º ano)',
                        '(Primeiramente explicar sobre o pagamento da mensalidade) Você falaria com o diretor
financeiro ou com algum voluntário caso não conseguisse pagar a mensalidade?',
                        'Você acredita merecer a dedicação de todos os voluntários? Por quê?',
                        'Por que devemos escolher você dentre tantos outros candidatos?',
                        'Se ex-aluno, por que te dar esta oportunidade novamente?',
                        'Se você não entrar no CATS, o que irá fazer?',
                    ],
                ]
        ]);

        $this->add([
            'name' => 'interviewSubmit',
            'type' => 'submit',
            'attributes' => [
                'class' => 'btn-flat btn-primary btn-block',
                'value' => 'Concluir',
            ]
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [
            'descIntro' => [
                'required' => false,
            ],
            'descSocioVul' => [
                'required' => false,
            ],
            'descStudent1' => [
                'required' => false,
            ],
            'descStudent2' => [
                'required' => false,
            ]
        ];
    }
}
