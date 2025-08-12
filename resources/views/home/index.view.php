<main class="flex-grow-1 mt-5 pt-4">
    <!-- Hero Section -->
    <style>
        .custom-bg-gradient {
            background: linear-gradient(rgba(0, 51, 102, 0.8), rgba(0, 51, 102, 0.8)),
                url('<?= asset("images/bank-bg.avif") ?>');
            background-size: cover;
            border-radius: 20px;
        }

        .feature-card {
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>

    <section class="custom-bg-gradient text-white py-5">
        <div class="container text-center py-5">
            <h2 class="display-4 mb-4">Bienvenido a Banco Fassil</h2>
            <p class="lead mb-4">Gestiona tus cuentas y operaciones bancarias de forma segura y sencilla.</p>
            <p class="lead mb-4">Accede a todas las funcionalidades para administrar tu dinero, consultar sucursales y revisar tu historial de transacciones.</p>
            <div class="d-flex gap-3 justify-content-center">
                <?php if (isAuth()): ?>
                    <a class="btn btn-primary btn-lg" href="<?= route('account.index') ?>">
                        <i class="bi bi-person-fill me-2"></i>Ir a mis cuentas
                    </a>
                <?php else: ?>
                    <a class="btn btn-success btn-lg" href="<?= route('login') ?>">
                        <i class="bi bi-person-check me-2"></i>Acceder
                    </a>
                <?php endif; ?>
                <a class="btn btn-outline-light btn-lg" href="<?= route('account.create') ?>">
                    <i class="bi bi-plus-circle me-2"></i>Abrir nueva cuenta
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-credit-card fs-1 text-primary mb-3"></i>
                        <h3 class="h4">Operaciones</h3>
                        <p>Realiza transferencias, pagos y otras operaciones bancarias de manera rápida y segura.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt fs-1 text-primary mb-3"></i>
                        <h3 class="h4">Sucursales</h3>
                        <p>Encuentra nuestras sucursales y cajeros automáticos en todo el país.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card card h-100 shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history fs-1 text-primary mb-3"></i>
                        <h3 class="h4">Historial</h3>
                        <p>Consulta el historial de tus movimientos y transacciones bancarias.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>