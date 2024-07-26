
<!doctype html>
<html lang="en" data-bs-theme="auto">
@include('chunk.head')
<body>
<div class="container">
    <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Главная</a></li>
            <li class="nav-item"><a href="{{ route('supplier.list') }}" class="nav-link" >Поставщики</a></li>
            <li class="nav-item"><a href="{{ route('client.list') }}" class="nav-link">Клиенты</a></li>
            <li class="nav-item"><a href="{{ route('contract.list') }}" class="nav-link">Договоры</a></li>
            <li class="nav-item"><a href="{{ route('entry.list') }}" class="nav-link">Поступления</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Списания</a></li>
        </ul>
    </header>
</div>
<script src="{{ asset('assets/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
