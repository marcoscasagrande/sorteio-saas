# Deploy no aaPanel (AWS) — via Git

Guia passo a passo pra colocar o SorteioSaaS no ar numa instância AWS com
aaPanel, usando Git tanto pra instalação quanto pra atualizações futuras.

## 0. Suba o projeto pro GitHub (ou GitLab/Bitbucket)

Este pacote já vem com um repositório git inicializado e o primeiro commit
feito. Você só precisa criar um repositório vazio no GitHub e apontar pra ele:

```bash
cd sorteio-saas
git remote add origin https://github.com/seu-usuario/sorteio-saas.git
git push -u origin main
```

Se o repositório for privado, gere um **Personal Access Token** no GitHub
(Settings > Developer settings > Personal access tokens) pra usar como senha
no clone/pull pelo servidor — assim não precisa configurar SSH.

## 1. Pré-requisitos no aaPanel

No painel do aaPanel, instale (App Store):
- **Nginx** (ou OpenLiteSpeed, se preferir)
- **MySQL** 8.0
- **PHP 8.3** (extensões: bcmath, ctype, curl, fileinfo, json, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, gd, zip)
- **Composer** (instalador direto no App Store do aaPanel)
- **Git** (App Store > procure "Git", ou confirme com `git --version` no terminal — costuma já vir instalado)
- **Supervisor Manager** (pra manter a fila `queue:work` rodando)

Node.js **não é necessário no servidor** — os assets (CSS/JS) já vêm
compilados e commitados no repositório.

## 2. Criar o site

**Website > Add Site**
- Domínio: `seudominio.com`
- Diretório: `/www/wwwroot/seudominio.com`
- PHP version: 8.3
- Marque para criar o banco de dados MySQL junto — anote usuário/senha gerados

## 3. Clonar o projeto via Git

Pelo terminal SSH do servidor (ou o Terminal web do próprio aaPanel):

```bash
cd /www/wwwroot
rm -rf seudominio.com          # remove a pasta vazia que o aaPanel criou
git clone https://github.com/seu-usuario/sorteio-saas.git seudominio.com
cd seudominio.com
```

Se pedir usuário/senha do GitHub, use o Personal Access Token como senha.

## 4. Rodar a instalação inicial

```bash
bash deploy/install.sh
```

Esse script pede pra você editar o `.env` (copiado de
`deploy/.env.production.example`), gera a `APP_KEY`, roda as migrations,
cria o usuário admin e prepara os caches de produção — tudo em um passo.

Preencha no `.env`: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (os que o
aaPanel gerou no passo 2), `MERCADOPAGO_ACCESS_TOKEN`, `INSTAGRAM_CLIENT_ID`
e `INSTAGRAM_CLIENT_SECRET`.

Login padrão do admin: `admin@sorteiosaas.com` / `troque-esta-senha` —
**troque a senha assim que entrar**.

## 5. Configurar o Nginx

Em **Website > seudominio.com > Config File**, substitua pelo conteúdo de
`deploy/nginx.conf.example`. O ponto mais importante: o `root` precisa
apontar para a pasta **`public/`** do projeto, não para a raiz.

## 6. Emitir SSL (HTTPS)

Em **Website > seudominio.com > SSL**, ative o Let's Encrypt (um clique).
Isso é **obrigatório** — o webhook do Mercado Pago e o OAuth do Instagram
exigem HTTPS.

## 7. Fila em background (Supervisor) — OBRIGATÓRIO

A busca de comentários do Instagram agora roda em fila. Sem o worker
rodando, todo sorteio criado fica preso em "buscando comentários" para
sempre. No plugin **Supervisor Manager**, crie um processo novo com o
conteúdo de `deploy/supervisor-queue.conf.example` (ajuste o caminho do
projeto).

## 8. Cron (agendador do Laravel)

Em **Cron Jobs**, crie uma tarefa "Shell Script" a cada minuto:

```bash
php /www/wwwroot/seudominio.com/artisan schedule:run >> /dev/null 2>&1
```

## 9. Webhook do Mercado Pago

No painel do Mercado Pago (Suas integrações > sua aplicação > Webhooks),
cadastre:

```
https://seudominio.com/webhooks/mercadopago
```

---

## Atualizações futuras (o dia a dia)

Depois da instalação inicial, o fluxo pra publicar qualquer mudança de
código é sempre:

**1. Na sua máquina, edite o código e suba pro GitHub:**
```bash
git add -A
git commit -m "descreva o que mudou"
git push
```

Se você alterou algo em `resources/` (CSS, views que mudam classes, JS), o
GitHub Action em `.github/workflows/build-assets.yml` builda os assets
automaticamente e commita `public/build` de volta — você não precisa ter
Node instalado. (Isso já vem configurado no repositório; não precisa fazer
nada além de ativar Actions no GitHub, que vem ativo por padrão.)

**2. No servidor, atualize:**
```bash
cd /www/wwwroot/seudominio.com
bash deploy/deploy.sh
```

Esse script coloca o site em manutenção por alguns segundos, dá `git pull`,
reinstala dependências PHP se o `composer.json` mudou, roda migrations
pendentes, recria os caches de produção, reinicia a fila e tira o site do
modo de manutenção — tudo automático.

Se você alterou o front-end e por algum motivo o GitHub Action não rodou
(ou você quer buildar direto no servidor), use:
```bash
bash deploy/deploy.sh --assets
```
(isso exige Node instalado no servidor, o que normalmente não é necessário).
