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
- Adicionada funcionalidade de inserção, autualização e remoção de mensalidades

### Removido
- Nada.

### Corrigido
- Corrigido erro que impedia a remoção da opção de idiomas ao imprimir os cartões de respostas.
- Pequenos erros de digitação em mensagens de erro.

## 2.0.0-alpha.1 - 2015-07-25
- Primeiro *commit*
