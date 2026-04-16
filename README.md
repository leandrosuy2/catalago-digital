# PedeUe - Catalogo Digital & Marketplace

Plataforma completa de **catalogo/cardapio digital** com marketplace multi-loja, gestao de pedidos, integracao com WhatsApp, programa de afiliados e multiplos gateways de pagamento.

Cada estabelecimento ganha sua propria **pagina** (`pedeue.com/nomeloja`) com vitrine personalizada, sacola de compras e fluxo de pedido completo — do delivery ao atendimento em mesa.

---

## Como Funciona o Roteamento

O sistema usa **path-based routing**: o primeiro segmento da URL identifica a loja ou cidade.

```
pedeue.com/pizzaria              -> Home da loja "pizzaria"
pedeue.com/pizzaria/categoria/5  -> Categoria 5 da loja
pedeue.com/pizzaria/sacola       -> Sacola de compras
pedeue.com/pizzaria/pedido       -> Fazer pedido

pedeue.com/saopaulo              -> Marketplace da cidade
pedeue.com/saopaulo/produtos     -> Produtos da cidade

pedeue.com/administracao         -> Painel admin
pedeue.com/painel                -> Painel do lojista
pedeue.com/login                 -> Login unificado
```

O `index.php` na raiz extrai o slug do path, consulta o banco (`estabelecimentos`, `cidades`, `subdominios`) e roteia para o modulo correto. Paths reservados como `administracao`, `painel`, `login`, `_core` nunca sao tratados como loja.

---

## Funcionalidades

### Loja do Estabelecimento

- Catalogo de produtos com categorias, busca e filtros
- Sacola de compras persistente por sessao
- Pedidos por modalidade: **Delivery**, **Balcao**, **Mesa** e **Outros**
- Acompanhamento de pedidos abertos e fechados em tempo real
- Integracao com **WhatsApp** para notificacao de pedidos
- **PWA** — instalavel como app nativo (manifest + service worker)
- Feed XML para integracao com shopping/marketplaces (`shopping.xml`)
- Paginas de estabelecimento fechado/desativado
- Personalizacao de cores, logo, capa, descricao e horarios
- Pedido minimo configuravel
- Taxa de entrega configuravel (fixa ou a combinar)

### Marketplace por Cidade

- Pagina por cidade (`pedeue.com/saopaulo`)
- Listagem de estabelecimentos por segmento
- Vitrine de produtos agregados de varias lojas
- Sacola unificada
- Selecao automatica de cidade por cookie

### Painel do Estabelecimento (`/painel`)

- Dashboard com pedidos em tempo real
- CRUD completo de **produtos** e **categorias** (com ordenacao)
- Gestao de **pedidos** (aceitar, concluir, imprimir, reembolsar)
- Configuracao de **frete** por regiao
- **Cupons** de desconto
- **Horarios de funcionamento** com abertura/fechamento automatico via cron
- Banners promocionais
- **QR Code** da loja
- Relatorios de vendas
- Impressao de comprovantes (integracao com impressoras termicas via API)
- Gestao de plano/assinatura
- Integracao com analytics (Google Analytics e Facebook Pixel)
- Configuracoes da loja (cores, contatos, endereco, redes sociais)

### Administracao (`/administracao`)

- Gestao global de **estados**, **cidades** e **segmentos**
- Gerenciamento de todos os **estabelecimentos** (criar, editar, ativar, bloquear, excluir)
- Controle de **planos e assinaturas**
- Emissao de **vouchers**
- Banners do marketplace
- Subdominios customizados
- Captacao de novos estabelecimentos
- Logs de atividade

### Programa de Afiliados (`/afiliado`)

- Cadastro e gestao de estabelecimentos indicados
- Controle de **vouchers** e **planos**
- Comissao configuravel (padrao: 10%)
- Painel de acompanhamento
- Estabelecimentos ativos e bloqueados

### Gateways de Pagamento

| Gateway | Recursos |
|---------|----------|
| **PIX** | Pagamento instantaneo com chave e beneficiario |
| **Mercado Pago** | Checkout, processamento e verificacao de status |
| **PagSeguro** | Checkout, sessao, processamento e notificacoes |
| **Getnet** | Checkout, processamento e status |

### Automacao (Cron)

- Abertura/fechamento automatico de lojas por agendamento (por dia da semana e horario)
- Sincronizacao de status de assinaturas com gateways de pagamento
- Cancelamento automatico de assinaturas expiradas/inadimplentes
- Reindexacao de estabelecimentos

### API de Impressao

- Endpoint REST para impressoras termicas (`/api/?token=...`)
- Autenticacao por token alfanumerico
- Retorna comprovante do pedido e atualiza status automaticamente
- Fila FIFO (primeiro pedido pendente do dia)

---

## Stack Tecnologica

| Camada | Tecnologia |
|--------|------------|
| **Back-end** | PHP 7.4+ |
| **Servidor** | Nginx + PHP-FPM (EasyPanel) |
| **Banco de Dados** | MySQL / MariaDB (`mysqli`) |
| **Front-end** | Bootstrap 3, jQuery, LineIcons |
| **E-mail** | PHPMailer (SMTP) |
| **Seguranca** | Google reCAPTCHA v2 |
| **PWA** | Service Workers + Web App Manifest |
| **Dependencias** | Composer (Mercado Pago SDK, Getnet SDK) |
| **Deploy** | EasyPanel (Git + Nixpacks) |

---

## Estrutura de Diretorios

```
sistema/
├── index.php                        # Router principal (path-based)
├── cron.php                         # Tarefas agendadas
├── 404.php                          # Pagina de erro
├── composer.json                    # Dependencias PHP
│
├── _core/                           # Nucleo compartilhado
│   ├── _includes/
│   │   ├── config.php               # Configuracao central (DB, URLs, gateways)
│   │   ├── functions.php            # Loader de funcoes
│   │   └── functions/
│   │       ├── user.php             # Autenticacao, sessao, restrict()
│   │       ├── data.php             # Helpers de dados / CRUD
│   │       ├── general.php          # Funcoes utilitarias
│   │       ├── db.php               # Conexao MySQL (mysqli + utf8mb4)
│   │       └── phpmailer/           # Biblioteca de e-mail
│   ├── _cdn/                        # Assets estaticos (CSS, JS, icones, fontes)
│   ├── _ajax/                       # Endpoints AJAX (sacola, cidades, checks)
│   ├── _uploads/                    # Uploads de midia (imagens de produtos/lojas)
│   └── _layout/                     # Layouts parciais do sistema
│
├── app/
│   ├── estabelecimento/             # Loja publica
│   │   ├── index.php                # Home da loja
│   │   ├── categoria.php            # Listagem por categoria
│   │   ├── produto.php              # Pagina do produto
│   │   ├── sacola.php               # Carrinho de compras
│   │   ├── pedido.php               # Selecao de modalidade
│   │   ├── pedido_delivery.php      # Pedido delivery
│   │   ├── pedido_balcao.php        # Pedido balcao
│   │   ├── pedido_mesa.php          # Pedido mesa
│   │   ├── pedido_outros.php        # Pedido outros
│   │   ├── pedidosabertos.php       # Pedidos em andamento
│   │   ├── pedidosfechados.php      # Historico de pedidos
│   │   ├── mercadopago/             # Gateway Mercado Pago
│   │   ├── pagseguro/               # Gateway PagSeguro
│   │   ├── getnet/                  # Gateway Getnet
│   │   ├── pix/                     # Gateway PIX
│   │   ├── integracao/              # Feed XML (shopping)
│   │   ├── _layout/                 # Templates (head, footer, nav, manifest)
│   │   ├── _ajax/                   # AJAX da loja (sacola, pedidos)
│   │   ├── css/                     # Estilos dinamicos
│   │   └── js/                      # Service Worker, scripts
│   │
│   └── cidade/                      # Marketplace por cidade
│       ├── index.php                # Home da cidade
│       ├── produtos.php             # Produtos agregados
│       ├── estabelecimentos.php     # Lista de lojas
│       ├── sacola.php               # Sacola da cidade
│       └── _layout/                 # Templates da cidade
│
├── administracao/                   # Painel administrativo (nivel 1)
│   ├── estabelecimentos/            # CRUD de lojas
│   ├── produtos/                    # Gestao de produtos
│   ├── categorias/                  # Gestao de categorias
│   ├── pedidos/                     # Gestao de pedidos
│   ├── planos/                      # Planos de assinatura
│   ├── assinaturas/                 # Assinaturas ativas
│   ├── vouchers/                    # Vouchers de desconto
│   ├── banners/                     # Banners
│   ├── segmentos/                   # Segmentos de mercado
│   ├── subdominios/                 # Subdominios customizados
│   └── configuracoes/               # Configuracoes globais
│
├── painel/                          # Painel do estabelecimento (nivel 2)
│   ├── produtos/                    # CRUD de produtos
│   ├── categorias/                  # CRUD de categorias
│   ├── pedidos/                     # Gestao de pedidos
│   ├── frete/                       # Configuracao de frete
│   ├── cupons/                      # Cupons de desconto
│   ├── horarios/                    # Horarios de funcionamento
│   ├── banners/                     # Banners da loja
│   ├── qrcode/                      # QR Code da loja
│   ├── impressao/                   # Impressao termica
│   ├── integracao/                  # Integracoes externas
│   ├── relatorio/                   # Relatorio de vendas
│   ├── plano/                       # Gestao de plano
│   └── configuracoes/               # Configuracoes da loja
│
├── afiliado/                        # Area do afiliado (nivel 3)
├── login/                           # Login unificado + PWA
├── esqueci/                         # Recuperacao de senha
├── localizacao/                     # Selecao de cidade
├── api/                             # API de impressao termica
├── conheca/                         # Landing page v1
├── conheca2/                        # Landing page v2
└── vendor/                          # Dependencias Composer
```

---

## Niveis de Acesso

| Nivel | Perfil | Area | Rota |
|-------|--------|------|------|
| **1** | Administrador | Backoffice global | `/administracao` |
| **2** | Estabelecimento | Painel da loja | `/painel` |
| **3** | Afiliado | Gestao de indicacoes | `/afiliado` |

O administrador (nivel 1) tem acesso a todas as areas do sistema.

A autenticacao usa sessoes PHP com keepalive via cookie para login persistente.

---

## Tabelas Principais do Banco

| Tabela | Descricao |
|--------|-----------|
| `users` | Usuarios do sistema (admin, lojista, afiliado) |
| `estabelecimentos` | Cadastro de lojas com slug, config, endereco, contato |
| `produtos` | Catalogo de produtos com preco, descricao, imagens |
| `categorias` | Categorias de produtos por loja |
| `pedidos` | Pedidos com comprovante, status, modalidade |
| `clientes` | Clientes finais que fazem pedidos |
| `planos` | Planos de assinatura disponiveis |
| `assinaturas` | Assinaturas ativas dos estabelecimentos |
| `estados` / `cidades` | Localidades do Brasil com slug |
| `segmentos` | Segmentos de mercado (alimentacao, varejo, etc.) |
| `subdominios` | Slugs customizados para lojas/cidades |
| `agendamentos` | Horarios de abertura/fechamento automatico |
| `frete` | Configuracao de frete por regiao |
| `cupons` | Cupons de desconto |
| `banners` / `banners_marketplace` | Banners promocionais |
| `vouchers` | Vouchers para planos |
| `impressao` | Fila de impressao termica |
| `pagamentos` | Registros de pagamentos |
| `logs` | Logs de atividade do sistema |

---

## Fluxo de Pedido

```
Cliente acessa pedeue.com/nomeloja
        |
        v
  Navega catalogo -> Adiciona a sacola
        |
        v
  Escolhe modalidade:
  +-----+-----+------+--------+
  v     v     v      v        v
Delivery Balcao Mesa  Outros  (dados de entrega/mesa)
  |
  v
  Seleciona forma de pagamento:
  +----+----+----+----+
  v    v    v    v    v
 PIX  MP  PagSeg Getnet WhatsApp
  |
  v
  Pedido registrado -> Notificacao WhatsApp
        |
        v
  Painel do lojista (/painel):
  Aceitar -> Preparar -> Concluir
        |
        v
  Impressao automatica (API termica)
```

---

## Deploy no EasyPanel

### Pre-requisitos

- Servidor com EasyPanel instalado
- Servico MySQL/MariaDB criado no EasyPanel

### Configuracao

1. Crie um novo projeto e adicione um servico **App** (tipo PHP)

2. Conecte o repositorio GitHub na aba **Git**

3. Na aba **NGINX**, configure o `root` para `/code` (nao `/code/public`):

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;

    root /code;

    index index.php index.html;

    server_name _;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* \.(woff|woff2|ttf|eot|otf)$ {
        add_header Access-Control-Allow-Origin *;
        try_files $uri =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass {{ fpm_socket }};
        fastcgi_param HTTPS on;
    }
}
```

4. Na aba **PHP**, selecione a versao **7.4** ou superior

5. Configure o banco de dados em `_core/_includes/config.php`:

```php
$db_host = "nome_do_servico_mysql";  // Host interno do EasyPanel
$db_user = "seu_usuario";
$db_pass = "sua_senha";
$db_name = "seu_banco";
```

6. Configure o dominio em `_core/_includes/config.php`:

```php
$simple_url = "seudominio.com";
```

7. Clique em **Deploy**

### Cron Jobs

Agende as tarefas automaticas (ajuste o token no `config.php`):

```bash
# Abertura/fechamento automatico de lojas (a cada minuto)
* * * * * curl -s "https://seudominio.com/cron.php?token=SEU_TOKEN&acao=agendamentos"

# Sync de assinaturas (a cada hora)
0 * * * * curl -s "https://seudominio.com/cron.php?token=SEU_TOKEN&acao=sync"

# Reindexacao (diaria as 03:00)
0 3 * * * curl -s "https://seudominio.com/cron.php?token=SEU_TOKEN&acao=reindex"
```

---

## Configuracao Local (Desenvolvimento)

### Requisitos

- PHP 7.4+
- MySQL 5.7+ ou MariaDB 10.3+
- Composer 2.x
- Nginx ou Apache com `mod_rewrite`

### Instalacao

```bash
git clone <url-do-repositorio>
cd sistema
composer install
```

Crie o banco e importe o schema. Configure `_core/_includes/config.php` com suas credenciais.

Se usar Nginx localmente, configure o `try_files` igual ao exemplo do EasyPanel. Se usar Apache, o `.htaccess` da raiz ja cuida do rewrite.

---

## Licenca

Projeto proprietario — todos os direitos reservados.
