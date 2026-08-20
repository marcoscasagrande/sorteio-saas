# Deploy no aaPanel (AWS)

Guia passo a passo pra colocar o SorteioSaaS no ar numa instância AWS com aaPanel.

## 1. Pré-requisitos no aaPanel

No painel do aaPanel, instale (App Store):
- **Nginx** (ou OpenLiteSpeed, se preferir)
- **MySQL** 8.0
- **PHP 8.3** (com as extensões: bcmath, ctype, curl, fileinfo, json, mbstring, openssl, pdo, pdo_mysql, tokenizer, xml, gd, zip — o aaPanel já marca a maioria por padrão nas config do PHP)
- **Composer** (tem um instalador direto no App Store do aaPanel)
- **Node.js** (versão 18+, também no App Store — necessário só se você for rodar `npm run build` no servidor; os assets já vêm compilados neste pacote)
- **Supervisor Manager** (pra manter a fila `queue:work` rodando)

## 2. Criar o site

1. **Website > Add Site**
   - Domínio: `seudominio.com`
   - Diretório: será algo como `/www/wwwroot/seudominio.com`
   - PHP version: 8.3
   - Crie o banco de dados MySQL junto (marque a opção) — anote usuário/senha gerados

2. Suba os arquivos deste pacote pra dentro de `/www/wwwroot/seudominio.com` (via FTP do aaPanel, ou `git clone` se você versionar o projeto).

## 3. Instalar dependências

Via terminal SSH (aaPanel tem terminal web também, em **Terminal**, no menu lateral):

```bash
cd /www/wwwroot/seudominio.com
composer install --no-dev --optimize-autoloader
```

Os assets (CSS/JS) já vêm compilados dentro de `public/build/` — não precisa rodar `npm run build` a menos que altere o visual.

## 4. Configurar o `.env`

```bash
cp deploy/.env.production.example .env
nano .env   # preencha DB_DATABASE, DB_USERNAME, DB_PASSWORD (os que o aaPanel gerou),
            # MERCADOPAGO_ACCESS_TOKEN, INSTAGRAM_CLIENT_ID/SECRET
php artisan key:generate
```

## 5. Migrar o banco e criar o admin

```bash
php artisan migrate
php artisan db:seed --class=AdminSeeder
```

Login padrão: `admin@sorteiosaas.com` / `troque-esta-senha` — **troque a senha assim que entrar** (crie uma tela de perfil, ou troque via `php artisan tinker`).

## 6. Configurar o Nginx

Em **Website > seudominio.com > Config File**, substitua pelo conteúdo de
`deploy/nginx.conf.example` deste pacote (ajuste a versão do PHP no socket
`fastcgi_pass` se necessário — confira em **PHP > versão instalada**).

O ponto mais importante: o `root` do site precisa apontar para a pasta
**`public/`** do Laravel, não para a raiz do projeto.

## 7. Emitir SSL (HTTPS)

Em **Website > seudominio.com > SSL**, use o Let's Encrypt gratuito — um clique
no aaPanel. Isso é **obrigatório**: o webhook do Mercado Pago e o OAuth do
Instagram exigem HTTPS.

## 8. Permissões

```bash
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

(o usuário `www` é o padrão do aaPanel pra rodar o PHP-FPM — confira em
**PHP > Configuração** se o seu ambiente usa outro usuário)

## 9. Fila em background (Supervisor)

No aaPanel, abra o plugin **Supervisor Manager** e crie um processo novo com
o conteúdo de `deploy/supervisor-queue.conf.example` (ajuste o caminho do
projeto). Isso mantém o `queue:work` sempre rodando, mesmo depois de reiniciar
o servidor.

## 10. Cron (agendador do Laravel)

Em **Cron Jobs** no aaPanel, crie uma tarefa "Shell Script" rodando a cada minuto:

```bash
php /www/wwwroot/seudominio.com/artisan schedule:run >> /dev/null 2>&1
```

Isso é o que vai permitir agendar, por exemplo, a renovação automática dos
tokens de Instagram antes de expirarem (ver TODO no README principal).

## 11. Webhook do Mercado Pago

No painel do Mercado Pago (Suas integrações > sua aplicação > Webhooks),
cadastre a URL:

```
https://seudominio.com/webhooks/mercadopago
```

## 12. Cache de produção

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

⚠️ Sempre que alterar o `.env` ou rotas depois disso, rode
`php artisan config:clear` (ou `config:cache` de novo) — senão o Laravel
continua usando o cache antigo.

## Deploys futuros

Depois da primeira instalação, pra cada atualização de código, rode:

```bash
bash deploy/deploy.sh
```

Esse script automatiza os passos 3, 5 e 12 acima.
