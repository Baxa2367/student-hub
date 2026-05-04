<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-bold">
            <i class="fas fa-list"></i> All Courses
        </h1>
    </div>
</div>

<?php if (isset($user) && $user['role'] === 'teacher'): ?>
    <div class="row mb-4">
        <div class="col-12">
            <a href="/student-hub/public/index.php?route=courses/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Course
            </a>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <?php if (count($courses) > 0): ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                        <p class="card-text text-muted">by <strong><?php echo htmlspecialchars($course['teacher_name']); ?></strong></p>
                        <p class="card-text"><?php echo substr(htmlspecialchars($course['description']), 0, 100) . '...'; ?></p>
                        <div class="mb-3">
                            <?php if (!empty($course['category'])): ?>
                                <span class="badge bg-secondary"><?php echo htmlspecialchars($course['category']); ?></span>
                            <?php endif; ?>
                            <span class="badge bg-primary"><?php echo $course['student_count']; ?> Students</span>
                        </div>
                        <a href="/student-hub/public/index.php?route=courses/view&id=<?php echo $course['id']; ?>" class="btn btn-primary">
                            View Course <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No courses available yet.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
