<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        p a {
            font-size: large;
            color: #000;
        }

        .container {
            /* margin-left: 100px; */
            width: 100%;
        }

        table {
            background-color: #f6f3f3;
            margin: auto;
        }

        th {
            width: 650px;
            height: 165px;
        }

        .redefinir {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 15px;
            background-color: #1687ab;
            border-color: #1687ab;
            padding: 10px 19px;
            margin: 10px auto;
            width: fit-content;
        }

        .redefinir a {
            color: #fff;
            margin-bottom: 25px;
        }

        .duvidas {
            text-align: center;
            margin-top: 25px;
            background-color: #1687ab;
            border-color: #1687ab;
            padding: 10px 19px;
            margin: auto;
            width: 145px;
        }

        .duvidas h2 {
            font-size: 15px;
            font-weight: 700;
            color: #000;
        }

        .duvidas a {
            color: #fff;
            margin-bottom: 15px;
        }

        .conteudo {
            padding: 40px 30px;
        }

        .conteudo p {
            color: #000;
        }

        .img-logo {
            margin-top: 45px;
            text-align: center;
        }

        .img_banner {
            /* background: url('https://mftacademy.com.br/banner-topo.jpg'); */
            width: 650px;
            height: 165px;
        }

        @media(max-width:600px) {
            .container {
                margin-left: 0px;
            }
            th {
                width: 250px;
                height: 63px;
            }
            .img_banner {
                /* background: url('https://mftacademy.com.br/banner-topo.jpg'); */
                background-position: center center;
                width: 300px;
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <thead>
                <th>
                    <div class="img_banner"></div>
                </th>
            </thead>
            <tr>
                <td>
                    <!-- Content here -->
                    <div class="conteudo">
                        <p>Olá, <b>{{ $nome }}</b>, tudo bem?</p>
                        <p>Recebemos sua solicitação para redefinir a <strong>sua senha de acesso</strong> ao Quero Auto.</p>
                        <p><strong>A solicitação ocorreu em:</strong> {{ $dataHora }}.</p>
                        <p>Se você reconhece essa ação, clique no botão abaixo para <strong>redefinir a sua senha.</strong>
                             Agora, se você <strong>não reconhece essa ação</strong> na sua conta, basta ignorar esse email.</p>
                        <p>Equipe Quero Auto </p>
                        <div class="redefinir">
                            <a href="{{ $url }}" role="button">Redefinir senha</a>
                        </div>
                    </div>
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
