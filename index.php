<?php
session_start();
$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
$errors = [];

// Each task in $_SESSION['tasks'] is expected to look like:
// ['id' => uniqid(), 'name' => '...', 'description' => '...']

// TODO (Prixy): handle POST action "add" -> save to $_SESSION['tasks'] with uniqid()
// TODO (Paul): request guarding + validation (trim, empty, strlen) -> fill $errors[]
// TODO: handle POST actions "update" and "delete" (match the task by task_id)
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Tasks | TaskFlow</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app">

    <!-- Sidebar -->
    <aside class="sidebar">
      <header class="logo">
        <img src="assets/taskflow_logo.png" alt="TaskFlow logo" class="logo-img">
      </header>

      <nav class="nav" aria-label="Task categories">
        <ul>
          <li class="nav-item active"><a href="index.php" aria-current="page"><img src="assets/tasks.png" alt="" class="nav-icon"><span>My Tasks</span></a></li>
        </ul>
      </nav>

      <footer class="user">
        <img src="assets/taskflow_user_icon.png" alt="User avatar" class="user-avatar">
        <span class="user-name">Claude</span>
        <img src="assets/taskflow_dropdown_icon.png" alt="" class="user-arrow">
      </footer>
    </aside>

    <!-- Main content -->
    <main class="main">
      <header class="page-header">
        <h1 class="page-title">My Tasks</h1>
        <button type="button" class="add-task-btn" id="add-task-btn" aria-controls="add-task-section" aria-expanded="<?= empty($errors) ? 'false' : 'true' ?>">+ Add Task</button>
      </header>

      <!-- Add task form (opened by the + Add Task button) -->
      <section class="add-task-section" id="add-task-section" aria-labelledby="add-task-title" <?= empty($errors) ? 'hidden' : '' ?>>
        <h2 class="section-title" id="add-task-title">Add a Task</h2>

        <?php foreach ($errors as $error): ?>
          <p class="form-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endforeach; ?>

        <form method="POST" class="task-form">
          <input type="hidden" name="action" value="add">

          <label for="task_name">Task Name</label>
          <input type="text" id="task_name" name="task_name" required>

          <label for="task_description">Task Description</label>
          <input type="text" id="task_description" name="task_description" required>

          <button type="submit" name="submit" class="save-btn">Add Task</button>
        </form>
      </section>

      <!-- Task list -->
      <section class="tasks" aria-labelledby="task-list-title">
        <h2 class="section-title" id="task-list-title">Your Tasks</h2>

        <?php if (empty($_SESSION['tasks'])): ?>
          <p class="empty-state">No tasks yet. Click "+ Add Task" to create one.</p>
        <?php endif; ?>

        <ul class="task-list" id="task-list">
          <?php foreach ($_SESSION['tasks'] as $task):
            $id          = htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8');
            $name        = htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8');
          ?>
            <li class="task-card">

              <!-- Read view -->
              <div class="task-view">
                <div class="task-info">
                  <h3 class="task-name"><?= $name ?></h3>
                  <p class="task-description"><?= $description ?></p>
                </div>
                <div class="task-actions">
                  <button type="button" class="btn-edit">Edit</button>
                  <form method="POST" class="delete-form">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="task_id" value="<?= $id ?>">
                    <button type="submit" class="btn-delete">Delete</button>
                  </form>
                </div>
              </div>

              <!-- Inline edit form (hidden until Edit is clicked) -->
              <form method="POST" class="edit-form" hidden>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="task_id" value="<?= $id ?>">

                <label for="edit_name_<?= $id ?>">Task Name</label>
                <input type="text" id="edit_name_<?= $id ?>" name="task_name" value="<?= $name ?>" required>

                <label for="edit_description_<?= $id ?>">Task Description</label>
                <input type="text" id="edit_description_<?= $id ?>" name="task_description" value="<?= $description ?>" required>

                <button type="submit" class="btn-save">Save</button>
                <button type="button" class="btn-cancel">Cancel</button>
              </form>

            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    </main>

  </div>

  <script src="js/script.js"></script>
</body>
</html>