<?php include __DIR__ . '/layout/header.php'; ?>

<!-- Hero Section -->
<div class="row mb-5">
    <div class="col-12">
        <div class="bg-primary text-white p-5 rounded-3">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-graduation-cap"></i> Welcome to Student Hub
            </h1>
            <p class="lead mb-0">Your complete online learning platform for teachers and students</p>
        </div>
    </div>
</div>

<!-- Statistics Row -->
<div class="row mb-5">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-book text-primary"></i>
                </h5>
                <h2 class="text-primary fw-bold"><?php echo count($courses); ?></h2>
                <p class="card-text">Active Courses</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-users text-success"></i>
                </h5>
                <h2 class="text-success fw-bold"><?php echo $teachersCount; ?></h2>
                <p class="card-text">Expert Teachers</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-comments text-info"></i>
                </h5>
                <h2 class="text-info fw-bold"><?php echo count($trendingPosts); ?></h2>
                <p class="card-text">Active Discussions</p>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-star"></i> Key Features
        </h2>
    </div>
    <div class="col-md-6 mb-3">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-check-circle text-success fa-2x"></i>
            </div>
            <div>
                <h5>Create & Manage Courses</h5>
                <p>Teachers can easily create and manage courses with detailed descriptions</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-check-circle text-success fa-2x"></i>
            </div>
            <div>
                <h5>Post Assignments</h5>
                <p>Create assignments with due dates and track student submissions</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-check-circle text-success fa-2x"></i>
            </div>
            <div>
                <h5>Collaborative Learning</h5>
                <p>Students can comment, discuss, and help each other learn</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="d-flex">
            <div class="me-3">
                <i class="fas fa-check-circle text-success fa-2x"></i>
            </div>
            <div>
                <h5>Real-time Notifications</h5>
                <p>Get instant notifications about course updates and new posts</p>
            </div>
        </div>
    </div>
</div>

<!-- Popular Courses -->
<div class="row mb-5">
    <div class="col-12 mb-4">
        <h2 class="fw-bold">
            <i class="fas fa-book-open"></i> Featured Courses
        </h2>
    </div>
    <?php foreach (array_slice($courses, 0, 3) as $course): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                    <p class="card-text text-muted">by <strong><?php echo htmlspecialchars($course['teacher_name']); ?></strong></p>
                    <p class="card-text"><?php echo substr(htmlspecialchars($course['description']), 0, 100) . '...'; ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary"><?php echo $course['student_count']; ?> Students</span>
                        <a href="/student-hub/public/index.php?route=courses/view&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                            View Course <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- CTA Section -->
<?php if (!isset($user) || !$user): ?>
    <div class="row">
        <div class="col-12">
            <div class="bg-light p-5 rounded-3 text-center">
                <h3 class="mb-3">Ready to Start Learning?</h3>
                <p class="lead mb-4">Join thousands of students in our community</p>
                <a href="/student-hub/public/index.php?route=auth/register" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus"></i> Register Now
                </a>
                <a href="/student-hub/public/index.php?route=auth/login" class="btn btn-outline-primary btn-lg ms-2">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
