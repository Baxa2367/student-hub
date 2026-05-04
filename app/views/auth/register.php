<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-5">
            <div class="card-body p-5">
                <h1 class="text-center mb-4">
                    <i class="fas fa-user-plus"></i> Register
                </h1>

                <form method="POST" action="/student-hub/public/index.php?route=auth/post-register">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo $_SESSION['old_input']['name'] ?? ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo $_SESSION['old_input']['email'] ?? ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="student" <?php echo ($_SESSION['old_input']['role'] ?? 'student') === 'student' ? 'selected' : ''; ?>>Student</option>
                            <option value="teacher" <?php echo ($_SESSION['old_input']['role'] ?? 'student') === 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirm" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-check"></i> Register
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <p>Already have an account? <a href="/student-hub/public/index.php?route=auth/login">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
