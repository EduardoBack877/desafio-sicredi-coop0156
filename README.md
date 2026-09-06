# Coop0156 — Sistema de Análise de Crédito Cooperativo

Desafio técnico desenvolvido em PHP/Laravel para o fluxo de cadastro de clientes, análise de crédito, consulta de score em Bureau, simulação das condições e contratação de crédito.

Durante a implementação procurei manter o fluxo simples de executar, com as regras de negócio separadas da camada HTTP, tratamento das falhas do serviço externo e cobertura automatizada dos principais cenários e limites das regras.

## Como rodar

### Requisitos

- Docker
- Docker Compose
- Linux, WSL2 ou ambiente compatível com Docker
- Git

### 1. Clone o repositório

```bash
git clone https://github.com/EduardoBack877/desafio-sicredi-coop0156.git
cd desafio-sicredi-coop0156
```

### 2. Crie o arquivo de ambiente

```bash
cp .env.example .env
```

O `.env.example` já contém a configuração utilizada pelo mock do Bureau no ambiente Sail:

```env
PHP_CLI_SERVER_WORKERS=4

SCORE_BUREAU_API_URL=http://localhost/api/mock/bureau
SCORE_BUREAU_TIMEOUT=3
```

### 3. Instale as dependências

Caso o projeto ainda não tenha a pasta `vendor`:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install
```

### 4. Crie o banco SQLite

```bash
touch database/database.sqlite
```

### 5. Suba o Laravel Sail

```bash
./vendor/bin/sail up -d
```

### 6. Gere a chave da aplicação

```bash
./vendor/bin/sail artisan key:generate
```

### 7. Execute as migrations

```bash
./vendor/bin/sail artisan migrate
```

### 8. Limpe o cache de configuração

```bash
./vendor/bin/sail artisan config:clear
```

### 9. Acesse a aplicação

```text
http://localhost
```

### Executar os testes

```bash
./vendor/bin/sail artisan test
```

Na última execução local:

```text
33 testes
129 assertions
```

### Encerrar o ambiente

```bash
./vendor/bin/sail down
```

---

## Stack

- PHP
- Laravel
- Laravel Sail
- SQLite
- Blade
- JavaScript
- Tailwind CSS
- Laravel HTTP Client
- PHPUnit / Laravel Testing
- Laravel Pint
- GitHub Actions
- Docker

---

## Fluxo da aplicação

```text
Cadastro / identificação do cliente
        ↓
Solicitação da análise
        ↓
Consulta ao Bureau
        ↓
Aplicação das regras de crédito
        ↓
Aprovado ou Reprovado
        ↓
Simulação
        ↓
Confirmação da contratação
```

---

## CRUD de clientes

Foi implementado o CRUD completo.

| Método | Endpoint | Ação |
|---|---|---|
| GET | `/api/clientes` | Lista clientes com paginação |
| POST | `/api/clientes` | Cadastra cliente |
| GET | `/api/clientes/{id}` | Consulta cliente |
| PUT | `/api/clientes/{id}` | Atualiza cliente |
| DELETE | `/api/clientes/{id}` | Exclui cliente |

### Validações

- nome obrigatório;
- CPF com exatamente 11 dígitos e único;
- e-mail válido e único;
- telefone opcional;
- renda mensal numérica e positiva.

As validações foram separadas em `FormRequest`.

Registros inexistentes retornam `404` e a exclusão realizada com sucesso retorna `204 No Content`.

---

## Análise de crédito

Endpoint:

```http
POST /api/analise-credito
```

Exemplo:

```json
{
    "nome": "Cliente Teste",
    "cpf": "12345678903",
    "renda_mensal": 10000,
    "tipo_credito": "pessoal",
    "valor_solicitado": 10000
}
```

Fluxo:

```text
Recebe solicitação
        ↓
Valida os dados
        ↓
Busca cliente pelo CPF
        ↓
Não existe?
        ↓
Cria automaticamente
        ↓
Persiste análise como PENDENTE
        ↓
Consulta Bureau
        ↓
Aplica regras de negócio
        ↓
Atualiza o resultado da análise
```

Quando o CPF ainda não pertence a um cliente, o cadastro é criado automaticamente e a análise fica vinculada a ele.

---

## Regras de crédito

| Condição | Resultado |
|---|---|
| Renda mensal < R$ 1.500,00 | Reprovado |
| Score < 400 | Reprovado |
| Score entre 400 e 699 | Aprovado — 4,5% a.m. |
| Score >= 700 | Aprovado — 2,9% a.m. |
| Parcela > 30% da renda mensal | Reprovado |

Motivos de reprovação utilizados:

```text
Renda mínima insuficiente
Score de crédito muito baixo
Comprometimento de renda superior a 30%
```

### Cálculo financeiro

São consideradas 12 parcelas fixas com juros simples.

```text
juros totais =
valor solicitado × taxa mensal × 12

valor total =
valor solicitado + juros totais

parcela =
valor total / 12
```

Exemplo para R$ 10.000,00 com taxa de 2,9%:

```text
Juros totais: R$ 3.480,00
Valor total:  R$ 13.480,00
Parcela:      R$ 1.123,33
```

---

## Integração com o Bureau

A comunicação com o Bureau foi isolada em:

```text
app/Services/BureauService.php
```

Configuração:

```env
SCORE_BUREAU_API_URL=http://localhost/api/mock/bureau
SCORE_BUREAU_TIMEOUT=3
```

A chamada é realizada com o HTTP Client do Laravel:

```php
Http::acceptJson()
    ->timeout($timeout)
    ->get(...);
```

A intenção foi evitar que detalhes da comunicação externa ficassem misturados às regras da análise de crédito.

### Tratamento de falhas

Uma indisponibilidade técnica do Bureau não é considerada reprovação do cliente.

```text
Falha do Bureau != Cliente reprovado
```

Cenários tratados:

| Situação | Resposta da aplicação |
|---|---|
| Erro HTTP do Bureau | `502 Bad Gateway` |
| Timeout / falha de conexão | `504 Gateway Timeout` |
| Resposta sem score válido | `502 Bad Gateway` |

Quando a análise já foi criada e ocorre uma dessas falhas, ela permanece:

```text
pendente
```

Assim não é tomada uma decisão de crédito com base em uma falha de infraestrutura.

---

## Organização do código

Estrutura principal da implementação:

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AnaliseCreditoController.php
│   │   └── ClienteController.php
│   │
│   └── Requests/
│       ├── SolicitarAnaliseCreditoRequest.php
│       ├── StoreClienteRequest.php
│       └── UpdateClienteRequest.php
│
├── Models/
│   ├── AnaliseCredito.php
│   └── Cliente.php
│
└── Services/
    ├── AnaliseCreditoService.php
    └── BureauService.php
```

### Controllers

Os Controllers ficaram responsáveis principalmente por:

- receber a requisição;
- delegar o processamento;
- tratar a camada HTTP;
- definir o status da resposta;
- retornar JSON.

### AnaliseCreditoService

Centraliza o fluxo da análise:

- busca ou criação do cliente;
- criação da análise;
- consulta do score;
- regra de renda mínima;
- regras de score;
- definição da taxa;
- cálculo da parcela;
- comprometimento de renda;
- aprovação;
- reprovação;
- contratação.

### BureauService

Responsável somente pela comunicação HTTP com o Bureau.

Essa separação reduz o acoplamento entre a integração externa e as regras de negócio.

---

## Criação automática do cliente

A solicitação de análise contém:

```text
nome
cpf
renda_mensal
tipo_credito
valor_solicitado
```

O fluxo não recebe e-mail.

Por isso foi adicionada uma migration permitindo `NULL` no campo `email` da tabela `clientes`.

No CRUD normal, porém, a validação continua exigindo o campo:

```text
POST /api/clientes
→ e-mail obrigatório
```

Preferi permitir `NULL` nesse cenário específico em vez de gerar um e-mail fictício para satisfazer apenas a restrição do banco.

---

## Interface de análise

Rota:

```text
/
```

A tela permite:

- enviar uma solicitação;
- visualizar estado de carregamento;
- tratar mensagens de erro;
- visualizar o score;
- visualizar aprovação ou reprovação;
- visualizar o motivo da reprovação;
- acessar a simulação quando o crédito é aprovado.

---

## Simulação

Rota:

```text
/simulacao/{id}
```

A tela apresenta:

- cliente;
- CPF;
- renda;
- tipo de crédito;
- score;
- taxa de juros;
- valor solicitado;
- juros estimados;
- valor total;
- número de parcelas;
- valor da parcela;
- percentual da renda comprometida.

A contratação somente é apresentada quando a análise está aprovada.

---

## Contratação

Endpoint:

```http
POST /api/analise-credito/{id}/contratar
```

Fluxo:

```text
aprovado
    ↓
contratado
```

Comportamento:

| Situação | Retorno |
|---|---|
| Análise aprovada | `HTTP 200` |
| Análise não aprovada | `HTTP 422` |
| Análise inexistente | `HTTP 404` |

No frontend o botão é desabilitado durante a requisição, evitando duplo envio enquanto a operação está sendo processada.

---

## Testes automatizados

A suíte utiliza:

```php
RefreshDatabase
Http::fake()
```

O banco é reiniciado entre os testes e a integração com o Bureau é simulada, evitando dependência de chamadas HTTP reais.

Executar:

```bash
./vendor/bin/sail artisan test
```

Última execução:

```text
33 testes
129 assertions
```

### Cenários de clientes

Entre os cenários cobertos:

- criação válida;
- campos obrigatórios;
- CPF duplicado;
- e-mail duplicado;
- listagem paginada;
- consulta por ID;
- registro inexistente;
- atualização parcial;
- exclusão;
- exclusão de registro inexistente.

### Cenários da análise

Entre os cenários cobertos:

- aprovação com score alto;
- aprovação com score médio;
- reprovação por renda;
- reprovação por score;
- reprovação por comprometimento;
- erro HTTP do Bureau;
- timeout / falha de conexão;
- resposta do Bureau sem score;
- criação automática de cliente;
- reutilização de cliente existente;
- contratação aprovada;
- tentativa de contratar análise reprovada;
- tentativa de contratar ID inexistente;
- validação dos campos obrigatórios.

### Testes de fronteira

Também foram adicionados testes específicos para os limites das regras:

```text
Score 399 → reprovado
Score 400 → taxa 4,5%

Score 699 → taxa 4,5%
Score 700 → taxa 2,9%

Renda 1499,99 → reprovado
Renda 1500,00 → passa pela regra de renda mínima

Parcela = 30% da renda → permitida
Parcela > 30% da renda → reprovada
```

A intenção é cobrir os pontos em que erros de comparação tendem a aparecer.

---

## Integração contínua

O repositório possui CI com GitHub Actions:

```text
.github/workflows/tests.yml
```

O workflow executa automaticamente nos eventos:

```text
push na main
pull request para main
```

Pipeline:

```text
Checkout
    ↓
Configuração do PHP
    ↓
Instalação das dependências
    ↓
Preparação do ambiente
    ↓
Migrations
    ↓
Testes automatizados
```

Assim uma alteração enviada ao repositório também é validada fora do ambiente local.

---

## Testando o mock do Bureau

O mock varia o comportamento conforme o último dígito do CPF.

Alguns CPFs úteis:

```text
12345678901 → score baixo
12345678902 → score médio
12345678903 → score alto
12345678904 → erro HTTP
12345678905 → resposta lenta / timeout
12345678906 → resposta sem score
```

### Cenário de aprovação

Use:

```text
Nome: Cliente Teste
CPF: 12345678903
Renda mensal: 10000
Valor solicitado: 10000
Tipo: pessoal
```

Resultado esperado:

```text
Score: 850
Taxa: 2,9% ao mês
Parcela: R$ 1.123,33
Status: aprovado
```

---

## Teste manual da API

```bash
curl -X POST http://localhost/api/analise-credito \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "nome": "Cliente Teste",
        "cpf": "12345678903",
        "renda_mensal": 10000,
        "tipo_credito": "pessoal",
        "valor_solicitado": 10000
    }'
```

---

## Observação sobre o mock no Sail

O Bureau disponibilizado no desafio é uma rota da própria aplicação:

```text
/api/mock/bureau/{cpf}
```

Durante:

```text
POST /api/analise-credito
```

a aplicação realiza outra requisição HTTP para o mock.

Por esse motivo o ambiente utiliza:

```env
PHP_CLI_SERVER_WORKERS=4
```

Assim um worker pode estar processando a análise enquanto outro responde à requisição do Bureau.

---

## Decisões de implementação

### Separação em Services

Evitei colocar comunicação HTTP, persistência e todas as regras financeiras diretamente no Controller.

Essa separação tornou o fluxo mais legível e facilitou a cobertura por testes.

### Falha do Bureau mantém a análise pendente

Se o Bureau estiver indisponível:

```text
Bureau indisponível
→ análise pendente
```

e não:

```text
Bureau indisponível
→ análise reprovada
```

A indisponibilidade de um serviço externo não deve ser transformada em uma decisão negativa de crédito.

### E-mail nullable na criação automática

O endpoint oficial de clientes continua exigindo e-mail.

Somente o fluxo automático da análise pode gerar o cadastro sem ele, porque esse dado não existe na solicitação de crédito.

### Contratação síncrona

O desafio apresenta fila como diferencial opcional.

Nesta versão mantive:

```text
aprovado → contratado
```

de forma síncrona e priorizei completar e testar o fluxo obrigatório.

---

## Possíveis evoluções

Em uma evolução para um ambiente de produção, eu consideraria:

- validação dos dígitos verificadores do CPF;
- autenticação e autorização;
- processamento assíncrono da contratação;
- idempotência na contratação;
- logs estruturados da comunicação com o Bureau;
- métricas de latência, timeout e indisponibilidade;
- trilha de auditoria para alterações de status;
- documentação OpenAPI/Swagger;
- PostgreSQL ou MySQL em produção;
- versionamento da API.

A validação matemática do CPF não foi aplicada nesta versão porque o mock fornecido no desafio utiliza CPFs específicos para determinar os diferentes cenários de score, erro e timeout.

---

## Autor

**Eduardo Back**