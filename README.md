# 🧩 INTEA — Uma Rede Social para a Comunidade Autista

![Banner INTEA](https://static-cdn.jtvnw.net/jtv_user_pictures/77929c41-b8da-4ddf-8f1f-35a8f6d73a59-profile_banner-480.jpeg)  

![Status](https://img.shields.io/badge/Status-🚧_Em_Desenvolvimento-yellow?style=for-the-badge)
![Laravel](https://img.shields.io/badge/Laravel-v10-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)

## 💙 Sobre o Projeto  
O **INTEA** é uma rede social inclusiva criada para fortalecer o vínculo entre **toda a comunidade autista**.  
Nosso foco é o **acolhimento, privacidade e interação saudável** entre os usuários, a plataforma oferece um ambiente seguro e intuitivo para troca de experiências e apoio mútuo.

## ✨ Funcionalidades

### 👤 Perfis Personalizados  
- Criação de perfil conforme o papel na comunidade:  
  - 🧠 Autista  
  - 🧍‍♂️ Responsável  
  - 💬 Membro da comunidade  
- Personalização com **foto e descrição**

### 📰 Feed e Postagens  
- Feed separado entre **autistas** e **comunidade/responsáveis** para maior privacidade  
- Postagens com **imagens, curtidas e respostas**  
- Exibição de conteúdo dentro de **comunidades específicas**

### 💬 Interação Social  
- Chat privado entre usuários  
- Sistema de curtidas e respostas  
- Comunidades segmentadas para conversas mais focadas
- Sistema de **denúncia** para mensagens ou postagens impróprias

## 🛠 Tecnologias Utilizadas

| Tipo | Nome | Descrição |
|------|------|------------|
| 🧩 **Framework** | ![Laravel](https://img.shields.io/badge/Laravel-v10-FF2D20?style=flat&logo=laravel&logoColor=white) | Framework backend moderno em PHP |
| 💻 **Linguagem** | ![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat&logo=php&logoColor=white) | Lógica principal do servidor |
| 🌐 **Frontend** | ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat&logo=html5&logoColor=white) ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat&logo=css3&logoColor=white) ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat&logo=javascript&logoColor=black) | Estrutura e comportamento visual |
| 🔔 **Biblioteca** | ![Pusher](https://img.shields.io/badge/Pusher-300D4F?style=flat&logo=pusher&logoColor=white) | Comunicação em tempo real |
| 🗄️ **Banco de Dados** | ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white) | Armazenamento de usuários, postagens e chats |

## 📁 Estrutura do Código

```plaintext
INTEA/
├── app/
│   ├── Http/Controllers/     # Lógica de controllers (CRUDs, regras de fluxo)
│   └── Models/               # Models e relações de tabelas
│
├── database/
│   ├── factories/            # Geração de dados falsos para testes
│   ├── migrations/           # Criação e alteração de tabelas
│   └── seeders/              # População inicial do banco
│
├── public/                   # Arquivos acessíveis publicamente (CSS, JS, imagens)
├── resources/views/          # Views Blade (interface visual)
├── routes/web.php            # Rotas principais da aplicação
└── README.md                 # Você está aqui!

```

## 🚀 Execução Local

### 📦 Passo a Passo

1. **Baixe o projeto** e extraia os arquivos.  
2. **Abra o terminal (CMD)** dentro da pasta do projeto.  
3. Execute os comandos abaixo em ordem:

```bash
composer install
npm install
npm run dev build
```

4. **Baixe o arquivo `.env`** e adicione-o na pasta raiz

[![Baixar .env](https://img.shields.io/badge/_Baixar_.env-1E90FF?style=for-the-badge&logo=files&logoColor=white)](https://www.mediafire.com/file/2wsbyk0ggoi1e00/.env/file)

5. **Execute as migrações e seeds**
```bash
php artisan migrate
php artisan migrate:refresh --seed
```
6. **Instale o Pusher**
```bash
composer require pusher/pusher-php-server
```

### 🖼️ Configuração de Imagens

1. **Após configurar o ambiente**, execute:
```bash
php artisan storage:link
php artisan serve
```
2. **Crie uma postagem com imagem**, depois pare a execução e repita:
```bash
php artisan storage:link
php artisan serve
```

#### 🗂️ Adicionar Imagens das Postagens
1. Agora a imagem deve aparecer no feed. Então, após fazer isso vá para: 
`public/storage/arquivos/postagens`
e adicione as imagens contidas no seguinte `.rar`

[![Baixar Imagens das Postagens](https://img.shields.io/badge/_Baixar_Imagens_Postagens-FF69B4?style=for-the-badge&logo=files&logoColor=white)](https://www.mediafire.com/file/lb6ia707tis01iw/ImagensTcc.rar/file)

#### 🧍‍♀️ Adicionar Imagens de Perfil
2. Para perfis, adicione a imagem manualmente e salve.
Em seguida, vá até:
`public/storage/arquivos/`
E adicione as imagens contidas no seguinte `.rar` acima.

<p align="center"> <b>Feito por INTEA</b></p> 
