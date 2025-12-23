# API de Controle de Estoque e Vendas

## Configurações Iniciais

Este projeto utiliza o Laravel Sail. É necessário ter o Docker instalado na máquina.

### Alias do Comando Sail

Para facilitar a execução do comando sail, adicione o seguinte alias:

```bash
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'
```

### Comandos para iniciar o projeto

Rodar as migrations do projeto
```bash
sail artisan migrate
```

Configurar os mocks de produtos
```bash
sail artisan seed:local-products
```

### queues

Para esse teste as queues estão utilizando a conexão do redis

para executar a fila de processamento de vendas utilize o seguinte comando
```bash
sail artisan queue:work --queue=sales --tries=10
```

para executar a fila de atualização de estoque utilize o seguinte comando
```bash
sail artisan queue:work --queue=inventory --tries=10
```
