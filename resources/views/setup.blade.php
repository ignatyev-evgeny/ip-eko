<!doctype html>
<html lang="en" class="h-100" data-bs-theme="auto">
@include('chunk.head')
<body class="d-flex h-100 text-center text-bg-dark">
    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <main class="mt-auto mb-auto px-3 ">
            <h1>Установка</h1>
            <p class="lead">Приложение успешно установлено.</p>
            <p class="lead">
                <a href="{{ route('index') }}" class="btn btn-lg btn-light fw-bold border-white bg-white">Использовать</a>
            </p>
        </main>
    </div>
    <script src="{{ asset('assets/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            BX24.installFinish();
        });
    </script>
</body>
</html>
