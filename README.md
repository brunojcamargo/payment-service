# Payment Service

Este projeto simula um serviço de transações financeiras entre usuários e logistas.

## Índice

- [Sobre](#sobre)
- [Instalação](#instalação)
- [Como Usar](#como-usar)
- [Testes](#testes)
- [Funcionamento](#funcionamento)

## Sobre

Neste projeto é possivel cadastrar usuários, realizar transações entre usuarios.

Usamos um moc para simular a autorização de pagamentos externa e envio de notificações para o Cliente.

O envio das notifições autorização e criação de carteira é realizado ultilizando filas de processamento.

Ultilizamos laravel em sua versão 10, PHP na versão 8.3, MariaDB como database e containers para entregar essa solução.


## Instalação

Siga estes passos para instalar o projeto:

Clone o repositório:

```bash
git clone https://github.com/brunojcamargo/payment-service
```
Entre no diretório do projeto

```bash
  cd payment-service
```

Crie o .env
```bash
    cp .env.example .env
```

Suba os containers
```bash
    docker compose up
```

## Como Usar

Após subir os containers, acesse a url http://127.0.0.1:8090 em seu navegador. Se a instalação foi bem sucedida, você terá o seguinte retorno:

```json
    {
        "run": true,
        "version": "alpha"
    }
```

Você pode acessar documentação dos endpoints do projeto [clicando aqui](https://documenter.getpostman.com/view/19570429/2sA2r824Sc#8591c6c4-b293-42b4-a80b-32674f03355d)

## Testes

Esse projeto possui alguns testes que garantem a validação dos dados obrigatórios nos endpoints e a comunição com os MOCs de notificação e validação de transações.

Pra executar os testes:

Suba os containers:
```bash
    docker compose up
```

Rode os testes:
```bash
    docker exec -it payment-app php artisan test
```

## Funcionamento

Sugestão de teste:

1. Cadastre dois usuários comuns.
2. Cadastre um usuário logista.
3. Realize depósito para ambos.
4. Realize transações entre eles.
5. Estorne transações.

Detalhes:

Ao cadastrar um usuário, é disparado um job para criar uma carteira para ele, sem ela, ele não poderá receber ou enviar dinheiro.

Ao depositar dinheiro, é disparado um job que valdia a transação em um MOC. Se for autorizado, o dinheiro entra na carteira. É possivel consultar o status da transação no endpoint GET /transfers.

O saldo dos usuários pode ser consultado no endpoint GET /users.

Apenas usuário comuns podem enviar dinheiro entre si, logistas podem apenas receber. Todas as transferencias disparam um job que valida a transações e se der tudo certo outro job para enviar a notificação é disparado. As transações podem ser consultadas no GET /transfers.

As transferencias entrem usuários podem ser estornadas a qualquer momento, desde que o usuário que recebeu tenha saldo disponivel e o usuario que enviou realize a solicitação.

O saldo disponivel para operaçoes é calculado da seguinte forma:
Saldo Disponivel na carteira - Somatória de transações de saida pendentes.

Clique [aqui](https://documenter.getpostman.com/view/19570429/2sA2r824Sc#8591c6c4-b293-42b4-a80b-32674f03355d) para ter acesso a documentação completa.