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
                'type' => 'Multicheckbox',
                'options' => [
                    'label' => 'Para iniciar a entrevista, e manter o clima leve,
                neste momento iniciaremos as apresentações.',
                    'value_options' => [
                        'Apresentem-se, falem sobre si mesmo, o que estuda, onde
                    estuda, quais atividades exerce.',
                        'Pergunte ao candidato o que ele conhece sobre nós, e,
                    posteriormente, explique nosso funcionamento de modo geral.',
                        'Fale para o candidato sobre a entrevista, explique sobre
                     esse momento inicial de apresentação, sobre o segundo
                     momento sobre a situação socioeconômica/vulnerabilidade, e um terceiro
                     sobre o candidato como estudante.',
                        'Leia sobre como o candidato conheceu o processo seletivo
                     e se conhece alguém que que já foi aluno. Peça para
                     ele comentar. A partir de agora, o foco da conversa estará
                     no candidato.'
                    ]
                ]
            ])
            ->add([
                'name' => 'descSocioVul',
                'type' => 'Multicheckbox',
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
                'type' => 'Multicheckbox',
                'options' => [
                    'label' => 'Aqui as perguntas são diretas, o formato da conversa
                será quase um quiz. O candidato irá se sentir pressionado e,
                provavelmente, ficará nervoso ou mais tímido.
                Esses sentimentos podem interferir negativamente nas
                respostas. Fique atento para não tornar o clima muito pesado.',
                    'value_options' => [
                        'Por que quer ser aluno?',
                        'Quais são seus objetivos?',
                        'Por que quer ingressar no ensino superior?',
                        'Já possui algum curso ou área em mente?',
                        'O que você acha que precisa fazer para conseguir alcançar
                    os seus objetivos (com a rotina que possui)?',
                        'Caso o candidato trabalhe ou esteja no 3º ano do ensino
                    médio verificar se ele terá tempo e disposição para estudar',
                        'Vai conseguir dar conta? Como?',
                    ],
                ]
            ])
            ->add([
                'name' => 'descStudent2',
                'type' => 'Multicheckbox',
                'options' => [
                    'label' => 'Sobre as nossas atividades:',
                    'value_options' => [
                        'Você frequentará as aulas de sábado?',
                        'Você frequentará as monitorias?',
                        'Você frequentará os simulados?',
                        'Você acredita que será capaz de manter o pagamento durante
                    o ano da mensalidade? Você assume o compromisso de manter
                    um diálogo com o diretor financeiro, caso não possa pagar?',
                        'Você acredita merecer a dedicação de todos os voluntários?
                     Por quê?',
                        'Por que devemos escolher você dentre tantos outros
                    candidatos?',
                        'Se ex-aluno, por que dar esta oportunidade a você
                    novamente?',
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
