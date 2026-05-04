<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card mt-5">
            <div class="card-body p-5">
                <h1 class="text-center mb-4">
                    <i class="fas fa-sign-in-alt"></i> Login
                </h1>

                <form method="POST" action="/student-hub/public/index.php?route=auth/post-login">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-lock-open"></i> Login
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p>Don't have an account? <a href="/student-hub/public/index.php?route=auth/register">Register here</a></p>
                </div>

                <hr>

                <div class="alert alert-info mt-3">
                    <h6 class="alert-heading">Demo Credentials:</h6>
                    <p class="mb-1"><strong>Teacher:</strong> john@example.com / password</p>
                    <p class="mb-0"><strong>Student:</strong> alice@example.com / password</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
