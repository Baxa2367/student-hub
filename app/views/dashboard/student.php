<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-bold">
            <i class="fas fa-graduation-cap"></i> Student Dashboard
        </h1>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary fw-bold"><?php echo count($courses); ?></h3>
                <p class="text-muted">Enrolled Courses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success fw-bold"><?php echo count($recentPosts); ?></h3>
                <p class="text-muted">My Posts</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-info fw-bold"><?php echo $unreadCount; ?></h3>
                <p class="text-muted">Notifications</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-warning fw-bold">4.5</h3>
                <p class="text-muted">Avg. Score</p>
            </div>
        </div>
    </div>
</div>

<!-- My Courses -->
<div class="row mb-5">
    <div class="col-12 mb-3">
        <h3 class="fw-bold"><i class="fas fa-book"></i> My Courses</h3>
    </div>
    <?php if (count($courses) > 0): ?>
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>
                        <p class="card-text text-muted">by <strong><?php echo htmlspecialchars($course['teacher_name']); ?></strong></p>
                        <div class="mb-3">
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?php echo $course['progress']; ?>%" 
                                     aria-valuenow="<?php echo $course['progress']; ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $course['progress']; ?>%
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Joined: <?php echo date('M d, Y', strtotime($course['joined_at'])); ?></small>
                        <br>
                        <a href="/student-hub/public/index.php?route=courses/view&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-eye"></i> View Course
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You're not enrolled in any courses yet. 
                <a href="/student-hub/public/index.php?route=courses">Browse courses</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Posts -->
<div class="row">
    <div class="col-12 mb-3">
        <h3 class="fw-bold"><i class="fas fa-comments"></i> My Recent Activity</h3>
    </div>
    <?php if (count($recentPosts) > 0): ?>
        <?php foreach ($recentPosts as $post): ?>
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                <p class="card-text text-muted">
                                    in <strong><?php echo htmlspecialchars($post['course_name']); ?></strong>
                                </p>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No recent activity.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
