<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="row mb-4">
    <div class="col-12">
        <a href="/student-hub/public/index.php?route=courses" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Back to Courses
        </a>
        <h1 class="fw-bold"><?php echo htmlspecialchars($course['name']); ?></h1>
        <p class="text-muted">by <strong><?php echo htmlspecialchars($course['teacher_name']); ?></strong></p>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Course Description</h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($course['description'])); ?></p>
                
                <?php if (isset($user) && $user['role'] === 'teacher' && $user['id'] === $course['teacher_id']): ?>
                    <div class="mt-3">
                        <a href="/student-hub/public/index.php?route=courses/edit&id=<?php echo $course['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Course
                        </a>
                        <a href="/student-hub/public/index.php?route=posts/create&course_id=<?php echo $course['id']; ?>" class="btn btn-success">
                            <i class="fas fa-plus"></i> Add Post
                        </a>
                        <button class="btn btn-danger" onclick="if(confirm('Delete this course?')) window.location.href='/student-hub/public/index.php?route=courses/delete&id=<?php echo $course['id']; ?>'">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                <?php elseif (isset($user) && $user['role'] === 'student' && !$isEnrolled): ?>
                    <a href="/student-hub/public/index.php?route=courses/enroll&id=<?php echo $course['id']; ?>" class="btn btn-success btn-lg mt-3">
                        <i class="fas fa-plus-circle"></i> Enroll in Course
                    </a>
                <?php elseif ($isEnrolled): ?>
                    <div class="alert alert-success mt-3">
                        <i class="fas fa-check-circle"></i> You are enrolled in this course
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Course Stats</h5>
                <div class="mb-3">
                    <p class="mb-1"><strong>Enrolled Students:</strong></p>
                    <h3 class="text-primary"><?php echo $course['student_count']; ?></h3>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Total Posts:</strong></p>
                    <h3 class="text-info"><?php echo $course['post_count']; ?></h3>
                </div>
                <div class="mb-3">
                    <p class="mb-1"><strong>Assignments:</strong></p>
                    <h3 class="text-warning"><?php echo $course['assignment_count']; ?></h3>
                </div>
                <div>
                    <p class="mb-1"><strong>Category:</strong></p>
                    <span class="badge bg-secondary"><?php echo htmlspecialchars($course['category'] ?? 'General'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <h3 class="fw-bold mb-3"><i class="fas fa-comments"></i> Course Posts</h3>
        
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <a href="/student-hub/public/index.php?route=posts/view&id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                    <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                                </a>
                                <p class="card-text text-muted">
                                    by <strong><?php echo htmlspecialchars($post['author_name']); ?></strong> 
                                    on <?php echo date('M d, Y H:i', strtotime($post['created_at'])); ?>
                                </p>
                                <p class="card-text"><?php echo substr(htmlspecialchars($post['content']), 0, 150) . '...'; ?></p>
                                <div>
                                    <span class="badge bg-secondary"><?php echo ucfirst($post['type']); ?></span>
                                    <span class="badge bg-info"><?php echo $post['comment_count']; ?> Comments</span>
                                    <?php if (!empty($post['due_date'])): ?>
                                        <span class="badge bg-danger">Due: <?php echo date('M d', strtotime($post['due_date'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="/student-hub/public/index.php?route=posts/view&id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary ms-2">
                                Read <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No posts in this course yet.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
