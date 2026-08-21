# Sorteio SaaS — Laravel

SaaS completo de sorteios: Home com SEO e CTA, Login/Registro (com 2FA por
e-mail para admins), Painel do usuário, Admin completo (usuários, relatório
de vendas, planos, logs de auditoria, configurações gerais), sorteio com
hash SHA-256 de auditoria, filtros de participação, múltiplos vencedores,
re-sorteio, selo embutível, busca de comentários em fila, revelação com
confete, planos por moeda ou uso ilimitado, e liberação automática via Pix
(Mercado Pago) — tudo com preços e limites editáveis pelo admin.

Este é um **projeto Laravel 11 completo** — `composer.json`, `artisan`,
`bootstrap/`, `public/index.php`, `config/`, `storage/`, tudo incluído.
Não precisa rodar `composer create-project` antes: é só clonar e seguir
a instalação abaixo.

---

## 🔑 Acesso padrão do admin

```
E-mail:  admin@sorteiosaas.com
Senha:   troque-esta-senha
```

Criado automaticamente pelo `AdminSeeder` durante a instalação.
**Troque essa senha assim que fizer o primeiro login** — vá em
Admin > (tela de perfil, ainda não implementada — por ora, troque via
`php artisan tinker` no servidor: `User::where('email','admin@sorteiosaas.com')->first()->update(['password' => bcrypt('sua-nova-senha')]);`).

O login do admin passa por **2FA por e-mail**: depois da senha, um código
de 6 dígitos é enviado pro e-mail cadastrado (expira em 10 min). Se o
`MAIL_*` do `.env` ainda não estiver configurado, o código aparece no log
da aplicação (`storage/logs/laravel.log`) em vez de travar o acesso.

---

## 📦 Instalação completa (servidor AWS + aaPanel)

### 1. Pré-requisitos no aaPanel

No painel do aaPanel, instale (App Store):
- **Nginx** (ou OpenLiteSpeed)
- **MySQL** 8.0
- **PHP 8.3** (extensões: bcmath, ctype, curl, fileinfo, json, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, gd, zip)
- **Composer**
- **Git**
- **Supervisor Manager** (mantém a fila rodando — obrigatório, ver abaixo)

Node.js **não é necessário no servidor** — o CSS/JS já vem compilado e
versionado no repositório.

### 2. Subir o projeto pro GitHub

Este pacote já vem com git inicializado e o histórico de commits pronto.
Crie um repositório vazio no GitHub e aponte pra ele:

```bash
cd sorteio-saas
git remote add origin https://github.com/seu-usuario/sorteio-saas.git
git push -u origin main
```

Repositório privado? Gere um **Personal Access Token** no GitHub
(Settings > Developer settings) pra usar como senha no clone/pull.

### 3. Criar o site no aaPanel

**Website > Add Site**
- Domínio: `seudominio.com`
- Diretório: `/www/wwwroot/seudominio.com`
- PHP version: 8.3
- Marque para criar o banco MySQL junto — **anote usuário e senha gerados**,
  você vai precisar deles no passo 5

### 4. Clonar o projeto no servidor

Pelo terminal SSH (ou o Terminal web do próprio aaPanel):

```bash
cd /www/wwwroot
rm -rf seudominio.com          # remove a pasta vazia criada pelo aaPanel
git clone https://github.com/seu-usuario/sorteio-saas.git seudominio.com
cd seudominio.com
```

### 5. Rodar a instalação

```bash
bash deploy/install.sh
```

Esse único comando faz tudo:
1. Copia `deploy/.env.production.example` para `.env` e pausa pra você editar
2. Roda `composer install`
3. Gera a `APP_KEY`
4. Ajusta permissões de `storage/` e `bootstrap/cache/`
5. **Roda `php artisan migrate` — é aqui que as tabelas são criadas no banco**
   (login e senha sozinhos no `.env` NÃO criam tabela nenhuma; é o `migrate`
   que lê `database/migrations/` e monta o schema inteiro)
6. Cria o usuário admin (`AdminSeeder`)
7. Gera os caches de produção

Quando o script pausar pedindo pra editar o `.env`, preencha pelo menos:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — os que o aaPanel gerou no passo 3
- `MAIL_*` — necessário pro código do 2FA do admin chegar por e-mail
- `MERCADOPAGO_ACCESS_TOKEN` — opcional aqui, dá pra cadastrar depois em Admin > Configurações
- `INSTAGRAM_CLIENT_ID` / `INSTAGRAM_CLIENT_SECRET` — quando for integrar a Graph API de verdade

**Se as tabelas já existiam e você só quer rodar as migrations de novo**
(sem reinstalar tudo), pode rodar isoladamente a qualquer momento:
```bash
php artisan migrate
```

### 6. Configurar o Nginx

Em **Website > seudominio.com > Config File**, substitua pelo conteúdo de
`deploy/nginx.conf.example`. O `root` precisa apontar para a pasta
**`public/`** do projeto, não para a raiz.

### 7. Emitir SSL (HTTPS) — obrigatório

Em **Website > seudominio.com > SSL**, ative o Let's Encrypt (um clique).
O webhook do Mercado Pago e o OAuth do Instagram exigem HTTPS.

### 8. Fila em background (Supervisor) — obrigatório

A busca de comentários do Instagram roda em fila. **Sem o worker rodando,
todo sorteio criado fica preso em "buscando comentários" pra sempre.**
No plugin **Supervisor Manager**, crie um processo com o conteúdo de
`deploy/supervisor-queue.conf.example` (ajuste o caminho do projeto).

### 9. Cron (agendador do Laravel)

Em **Cron Jobs**, crie uma tarefa "Shell Script" a cada minuto:
```bash
php /www/wwwroot/seudominio.com/artisan schedule:run >> /dev/null 2>&1
```

### 10. Webhook do Mercado Pago

No painel do Mercado Pago (Suas integrações > Webhooks), cadastre:
```
https://seudominio.com/webhooks/mercadopago
```

---

## 🔄 Atualizações depois da instalação

```bash
# na sua máquina
git add -A && git commit -m "descreva o que mudou" && git push
# no servidor
cd /www/wwwroot/seudominio.com
bash deploy/deploy.sh
```

O `deploy.sh` põe o site em manutenção, dá `git pull`, reinstala
dependências se `composer.json` mudou, **roda as migrations pendentes**,
recria os caches, reinicia a fila e libera o site de novo — automático.

Se você alterou `resources/` (CSS/JS/views), o GitHub Action
(`.github/workflows/build-assets.yml`) já builda e commita os assets
sozinho a cada push — não precisa Node no servidor.

---

## ✉️ E-mail na AWS — leia antes de configurar o `MAIL_*`

Se o seu servidor é uma instância **EC2 da AWS** (o que costuma acontecer
por trás do aaPanel), configurar e-mail é diferente de um servidor comum:

**A AWS bloqueia a porta 25 (SMTP direto) por padrão** em toda instância
EC2, pra evitar spam saindo da própria rede. Isso significa que:
- Um SMTP genérico configurado na porta 25 vai falhar silenciosamente
- Você precisa usar a **porta 587** (submission com TLS) com um provedor
  de e-mail transacional — não dá pra só apontar pro seu servidor de e-mail
  comum como faria numa VPS qualquer

**Recomendado: Amazon SES**
1. No console da AWS, abra o **SES** (Simple Email Service)
2. Verifique seu domínio ou e-mail remetente (SES > Verified identities)
3. Gere **credenciais SMTP** específicas em SES > SMTP Settings > "Create
   SMTP credentials" — são diferentes das suas chaves de acesso normais da AWS
4. Preencha no `.env`:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=email-smtp.SEU-REGIAO.amazonaws.com
   MAIL_PORT=587
   MAIL_USERNAME=<usuário SMTP gerado pelo SES>
   MAIL_PASSWORD=<senha SMTP gerada pelo SES>
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=contato@seudominio.com
   ```
5. **Atenção ao modo sandbox**: contas novas do SES só enviam pra e-mails
   verificados manualmente, até você pedir liberação de produção
   (SES > Account dashboard > "Request production access" — é gratuito,
   leva algumas horas pra aprovar)

Sem isso configurado corretamente, tanto o **2FA do admin** quanto o
**"esqueci minha senha"** continuam funcionando pro fluxo em si, mas o
e-mail não chega — o código/link cai no `storage/logs/laravel.log` em vez
de ser entregue.

## 🔓 Esqueci minha senha

Já implementado — fluxo padrão do Laravel:
- `/esqueci-senha` — usuário informa o e-mail, recebe um link válido por
  60 minutos (`/redefinir-senha/{token}?email=...`)
- Mensagem de confirmação sempre igual, exista ou não o e-mail cadastrado —
  evita que alguém descubra quais e-mails têm conta
- Toda solicitação e toda redefinição ficam registradas em Admin > Logs

Requer a mesma configuração de `MAIL_*` da seção acima.

---

## O que tem no admin

- **Painel** — métricas gerais
- **Usuários** — lista com busca, detalhe por usuário
- **Vendas** (`/admin/relatorio-de-vendas`) — filtro por período, faturamento,
  ticket médio, gráfico diário, lista de pagamentos
- **Planos** (`/admin/planos`) — CRUD de dois tipos: **moedas** (pacote pago
  uma vez, cada sorteio consome 1 moeda) e **uso ilimitado** (mensal ou
  anual, todo sorteio liberado automaticamente enquanto ativo)
- **Logs de auditoria** (`/admin/logs`) — login, sorteios, pagamentos
- **Configurações** (`/admin/configuracoes`) — nome do site, tagline, logo,
  SEO, limite grátis de comentários, valor do sorteio avulso, chaves do
  Mercado Pago — tudo editável sem mexer no servidor

## Sorteio: recursos

- Fila em background pra buscar comentários (não trava a requisição)
- Filtros de participação (menção, hashtag, seguir a conta)
- Múltiplos vencedores por sorteio (1 a 20)
- Preview dos comentários elegíveis antes de sortear
- Hash SHA-256 + link público de comprovação (`/verificar/{hash}`)
- Liberação por moeda, acesso ilimitado ou Pix avulso
- Selo HTML embutível
- Re-sorteio com histórico registrado
- Revelação com confete

## Segurança

- 2FA por e-mail no login do admin
- Logs de auditoria para ações sensíveis

## SEO

- Título, descrição e Open Graph configuráveis (via admin)
- `sitemap.xml` e `robots.txt` dinâmicos

## Estrutura do que está incluído

- `app/` — Models, Controllers, Middleware, Jobs, Services
- `database/migrations` + `database/seeders/AdminSeeder.php`
- `resources/views` — todas as telas, já estilizadas
- `public/build/` — CSS/JS já compilados e versionados no git
- `.github/workflows/build-assets.yml` — rebuild automático dos assets
- `deploy/` — Nginx, Supervisor, `.env` de exemplo, `install.sh`, `deploy.sh`

⚠️ **Vendor não incluído**: meu ambiente não tem acesso ao Packagist, só ao
npm — a pasta `vendor/` do Laravel não está no pacote. `deploy/install.sh`
roda o `composer install` por você.

## Fora do escopo por enquanto

- **Vídeo de reveal para Stories** — exige geração de vídeo (ffmpeg/serviço externo)
- **Blog/CMS de conteúdo para SEO** — merece um editor de posts próprio
- **Cobrança recorrente automática** dos planos ilimitados — hoje é compra
  manual repetida (o sistema mostra a data de validade); assinatura de
  verdade exigiria a API `preapproval` do Mercado Pago

## O que falta implementar (TODOs no código)

- `FetchGiveawayComments::handle()` — chamada real à Instagram Graph API
  (`GET /{media-id}/comments`), com paginação, usando o token em
  `InstagramToken`. Formato esperado por comentário:
  `['username', 'text', 'mentions', 'is_follower']`
- Verificação de e-mail (confirmar que o e-mail do usuário existe de fato)
- Job agendado pra renovar tokens do Instagram antes de expirar
- App Review da Meta, antes de liberar conexão de Instagram sem tester manual
