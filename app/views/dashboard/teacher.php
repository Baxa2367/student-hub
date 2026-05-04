<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="fw-bold">
            <i class="fas fa-tachometer-alt"></i> Teacher Dashboard
        </h1>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-primary fw-bold"><?php echo count($courses); ?></h3>
                <p class="text-muted">My Courses</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <h3 class="text-success fw-bold"><?php echo count($recentPosts); ?></h3>
                <p class="text-muted">Posts Created</p>
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
                <h3 class="text-warning fw-bold"><?php echo count($user); ?></h3>
                <p class="text-muted">Profile Views</p>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <a href="/student-hub/public/index.php?route=courses/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Course
        </a>
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
                        <p class="card-text text-muted"><?php echo substr(htmlspecialchars($course['description']), 0, 80) . '...'; ?></p>
                        <div class="mb-3">
                            <span class="badge bg-primary"><?php echo $course['student_count']; ?> Students</span>
                            <span class="badge <?php echo $course['is_published'] ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo $course['is_published'] ? 'Published' : 'Draft'; ?>
                            </span>
                        </div>
                        <a href="/student-hub/public/index.php?route=courses/view&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="/student-hub/public/index.php?route=courses/edit&id=<?php echo $course['id']; ?>" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="/student-hub/public/index.php?route=posts/create&course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Post
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> You haven't created any courses yet. 
                <a href="/student-hub/public/index.php?route=courses/create">Create one now</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Posts -->
<div class="row">
    <div class="col-12 mb-3">
        <h3 class="fw-bold"><i class="fas fa-comments"></i> Recent Posts</h3>
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
                                <span class="badge bg-secondary"><?php echo ucfirst($post['type']); ?></span>
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
                <i class="fas fa-info-circle"></i> You haven't created any posts yet.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
