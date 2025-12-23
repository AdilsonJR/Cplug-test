# API de Controle de Estoque e Vendas

## Configurações Iniciais

Este projeto utiliza o Laravel Sail. É necessário ter o Docker instalado na máquina.

### .env

o arquivo .env.example tem exatamente as variáveis de ambiente necessárias para rodar o projeto no ambiente local 
sendo necessário apenas fazer uma cópia e renomear para .env


### Alias do Comando Sail

Para facilitar a execução do comando sail, adicione o seguinte alias:

```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

### Comandos para iniciar o projeto

Subir os containers do projeto
```bash
sail up -d
```

Rodar as migrations do projeto
```bash
sail artisan migrate
```

Configurar os mocks de produtos
```bash
sail artisan seed:local-products
```

### scheduler

Para executar as tarefas agendadas localmente
```bash
sail artisan schedule:work
```

### queues

Para este sistema eu utilizei o redis como gestor de cache e queue, para projetos menores ele não deixa a desejar, estou utilzando os contratos nativos do laravel, sendo facil de migrar para outro serviço de fila mais robusto futuramente.

para executar a fila de processamento de vendas utilize o seguinte comando
```bash
sail artisan queue:work --queue=sales --tries=10
```

para executar a fila de atualização de estoque utilize o seguinte comando
```bash
sail artisan queue:work --queue=inventory --tries=10
```

### testes

para executar a stack de testes utilize o seguinte comando
```bash
sail artisan test
```

### variaveis de ambiente

- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `INVENTORY_PRUNE_DAYS=90`
- `INVENTORY_PRUNE_AT=02:00`
- `REDIS_HOST=redis` (Sail)

### base url

- `http://localhost`

## Endpoints

- `POST /api/inventory` registrar entrada de estoque
- `GET /api/inventory` consultar estoque
- `POST /api/sales` registrar venda
- `GET /api/sales/{id}` detalhes da venda
- `GET /api/reports/sales` relatório de vendas com filtros

## Otimizações aplicadas

- Cache de estoque por produto com atualização ao alterar estoque/preço.
- GET `/api/inventory` reduz consultas ao banco consultando apenas produtos que o cache expirou.
‑ Consultas com eager loading e select restrito para evitar N+1 em vendas e relatórios.
‑ Jobs em filas separadas (sales e inventory) para processamento e atualização assíncrona, utilizando lockForUpdate para evitar conflitos de race condition.
‑ Task agendada para limpeza de estoque antigo (90 dias), está configurada para rodar as 2h da madrugada todos os dias uma única vez, mas se for necessário é possível trocar o horário através das variáveis de ambiente.

## Estrutura do projeto
Utilizei uma estrutura simples para manutenção de projetos pequenos, sendo fácil de escalar futuramente caso o projeto fique maior, se necessário é possível estruturar por módulos e/ou implementar os adapters e migrar para Clean Architecture 

- `app/Http/Controllers`: camada HTTP, apenas orquestra requests e responses.
- `app/Http/Requests`: validação e padronização de erros.
- `app/DTOs`: objetos de transferência para payloads explícitos.
- `app/UseCases`: regras por caso de uso, desacoplando controller de service.
- `app/Services`: lógica de domínio e orquestração de regras.
- `app/Repositories`: acesso a dados e queries otimizadas.
- `app/Jobs` e `app/Listeners`: processamento assíncrono e integrações com eventos.

## Testes implementados

- `tests/Unit/InventoryServiceTest.php`: entrada de estoque, atualização de custo e cache por produto.
- `tests/Unit/ProcessSaleJobTest.php`: cálculo de totais/lucro e atualização de unit_cost em múltiplos itens.
- `tests/Unit/UpdateInventoryJobTest.php`: baixa de estoque e atualização do cache.
- `tests/Unit/SalesReportServiceTest.php`: filtro por SKU e retorno de todos os itens da venda, com totais.
- `tests/Unit/SaleEventDispatchTest.php`: disparo de evento e enfileiramento dos jobs.
- `tests/Feature/RequestValidationTest.php`: validações de request para inventory, sales e reports.
