# Changelog

Todas as mudanças notáveis serão documentadas neste arquivo em ordem cronológica inversa.

## 2.0.0-alpha.3 - 2016-09-01

### Adicionado

### Corrigido
- Correções na geração de provas.
- Melhorias diversas no script de instalação.
- Melhorias na geração de carteirinhas.

## 2.0.0-alpha.2 - 2016-08-01

### Adicionado
- Biblioteca php5-intl ao script para que o validator IsFloat do ZF2 funcione.
- CRUD para tipos de fluxos de caixa (CashFlowType).
- Eventos de abertura e fechamento de balanços mensais (MonthlyBalance).
- Possibilidade de exclusão de balanços mensais se não houverem fluxos de caixa associados.
- CRUD para fluxos de caixa (CashFlow), com restrições quanto a edição e exclusão.
- Gráfico em 'Expense and Revenue' mostrando as despesas e receitas dos últimos 12 meses.
- Adicionada funcionalidade de inserção, atualização e remoção de mensalidades e integração com os fluxos de caixa.
- Exibição do quadro de alunos de uma turma selecionada.
- Exibição de aviso sobre a abertura de processos seletivos no site.
- Melhorias na manipulação de mensalidades.
- [#67](https://github.com/CATSInformatica/CatsSys/issues/67) adição de escolha da quantidade de questões e numeração (escolha do número incial).
- [#63](https://github.com/CATSInformatica/CatsSys/issues/63) faz a compactação de todos os cartões de respostas antes e faz o download de um único arquivo compactado.
- Borda inferior nas linhas da tabela de mensalidade para facilitar a diferenciação dos campos de pagamento.
- Envio de email para candidatos inscritos em processos seletivos de alunos.
- Funcionalidades de pré-entrevista, entrevista e análise de candidatos para processos seletivos de alunos.
- Adição de opção de utilização do sistema com Nginx
- Criação de novas telas para gestão de matrículas de alunos.
- 

### Removido
- *Action* edital do controller Recruitment foi renomeada para *publicNotice* sendo necessário alterar o privilégio do *guest* de `edital` para `public-notice`

### Corrigido
- Corrigido erro que impedia a remoção da opção de idiomas ao imprimir os cartões de respostas.
- Pequenos erros de digitação em mensagens de erro.
- Erro na lista de presença ao utilizar a versão 5.5 do php
- [#78](https://github.com/CATSInformatica/CatsSys/issues/78) ajustes nas alternativas do cartão de respostas para evitar detecção automática indevida.
- O valor adicionado como mensalidade passa a ser contabilizado na receita do mês.
- [#102](https://github.com/CATSInformatica/CatsSys/issues/102) padronizada a forma como o texto de ajuda é exibido na tela de envio de listas de presença.
- [#103](https://github.com/CATSInformatica/CatsSys/issues/103) aplicação do datatables na tabela de recursos.
- [#107](https://github.com/CATSInformatica/CatsSys/issues/107) Alterarado o texto do botão de salvar advertências.
- [#108](https://github.com/CATSInformatica/CatsSys/issues/108) Aumentar a quantidade de linhas no comentário de advertências.
- [#124](https://github.com/CATSInformatica/CatsSys/issues/124) Alterar exibição do mês correspondente no fomulário de cadastro de despesas/receitas.
- [#125](https://github.com/CATSInformatica/CatsSys/issues/125) Nomear a não escolha de um departamento específico ao cadastrar uma despesa/receita.
- [#126](https://github.com/CATSInformatica/CatsSys/issues/126) Aumentar a data final de processos seletivos de alunos e voluntários para desenvolvedores.
- [#127](https://github.com/CATSInformatica/CatsSys/issues/127) Resposta correta não é salva ao cadastrar ou editar questões.
- Disponibilizada a versão completa da [licença](https://github.com/CATSInformatica/CatsSys/blob/master/LICENSE) GPL no repositório
- Botões utilizados para alteração da situação dos candidatos de processos seletivos de alunos foram retirados da página do perfil do candidato e colocados na página de inscrições.
- Melhoria na geração de provas, permitindo que as provas sejam salvas e modificadas.

## 2.0.0-alpha.1 - 2015-07-25
- Primeiro *commit*
