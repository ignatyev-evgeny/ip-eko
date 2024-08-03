
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Главная</a></li>
{{--            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link" >Поставщики</a></li>--}}
{{--            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link">Клиенты</a></li>--}}
            <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
            <li class="nav-item"><a href="{{ route('write-off.list') }}" class="nav-link">Списания</a></li>
        </ul>
    </header>
    <style>
        .col {
            margin-top: 15px;
        }
    </style>
    <main class="m-auto">
        <div class="row row-cols-1 row-cols-lg-5 align-items-stretch text-center">
            <div class="col">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="lead">Поступления</div>
                        <h2 class="card-title">{{ $entries }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="lead">Списания</div>
                        <h2 class="card-title">{{ $writeoffs }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="lead">Договоров</div>
                        <h2 class="card-title">{{ $contracts }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="lead">Клиентов</div>
                        <h2 class="card-title">{{ $clients }}</h2>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card rounded-4">
                    <div class="card-body">
                        <div class="lead">Поставщиков</div>
                        <h2 class="card-title">{{ $suppliers }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="{{ asset('assets/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
