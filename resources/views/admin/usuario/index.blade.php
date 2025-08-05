@extends('admin.template.layout')

@section('main')
<h1 class="mb-4">Gerenciamento de Usuários</h1>
<div class="card">
    <div class="card-header d-flex justify-content-between align-itens-center">
        <h4 class="mb-0">Usuários Cadastradas</h4>
        <a class="btn btn-success" href="#"><i class="bi bi-plus"></i>Nova</a> <!-- img -->
    </div>
    <div class="card-body">

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">nome</th>
                    <th scope="col">user</th>
                    <th scope="col">email</th>
                    <th scope="col">data nascimento</th>
                    <th scope="col">tipo usuário</th>
                    <th scope="col">status conta</th>
                    <th scope="col">ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usuario as $item)
                <tr>
                    <th class="align-middle" scope="row">{{ $item->id }}</th>
                    <td class="align-middle">{{ $item->nome }}</td>
                    <td class="align-middle">{{ $item->user }}</td>
                    <td class="align-middle">{{ $item->email }}</td>
                    <td class="align-middle">{{ \Carbon\Carbon::parse($item->data_nascimento)->format('d/m/Y') }}</td>
                    <td class="align-middle">{{ $item->tipo_usuario}}</td>
                    <td class="align-middle">{{ $item->status_conta}}</td>

                    <td class="align-middle">
                        <form action="" method="post">
                            @csrf
                            @method("delete")
                            <button onclick="if (confirm('Você tem certeza que deseja excluir este Registro?')) {this.form.submit}" type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr>

        <div class="d-flex justify-content-center"> <!-- Paginação-->

            {{ $usuario->links()}}

        </div>

    </div>
</div>
@endsection