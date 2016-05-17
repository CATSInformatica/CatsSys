# Changelog

Todas as mudanças notáveis serão documentadas neste arquivo em ordem cronológica inversa.

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

### Removido
- *Action* edital do controller Recruitment foi renomeada para *publicNotice* sendo necessário alterar o privilégio do *guest* de `edital` para `public-notice`

### Corrigido
- Corrigido erro que impedia a remoção da opção de idiomas ao imprimir os cartões de respostas.
- Pequenos erros de digitação em mensagens de erro.
- Erro na lista de presença ao utilizar a versão 5.5 do php
- [#78](https://github.com/CATSInformatica/CatsSys/issues/78) ajustes nas alternativas do cartão de respostas para evitar detecção automática indevida.
- O valor adicionado como mensalidade passa a ser contabilizado na receita do mês.

## 2.0.0-alpha.1 - 2015-07-25
- Primeiro *commit*
