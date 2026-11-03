<?php
session_start();
$_SESSION['tasks'] = $_SESSION['tasks'] ?? [];
$errors = [];

// ===== DEMO ONLY: sample tasks to preview the layout. DELETE this block before final submission. =====
if (empty($_SESSION['demo_loaded'])) {
    $_SESSION['tasks'] = [
        ['id' => uniqid(), 'name' => 'Finish Chapter 1', 'description' => 'Identify the Scope and Delimitations'],
        ['id' => uniqid(), 'name' => 'Buy groceries',    'description' => 'Milk, eggs, bread, and butter'],
        ['id' => uniqid(), 'name' => 'Read a Book',      'description' => 'Finish 2 chapters of Atomic Habits'],
    ];
    $_SESSION['demo_loaded'] = true;
}
// ===== END DEMO =====

// Each task in $_SESSION['tasks'] is expected to look like:
// ['id' => uniqid(), 'name' => '...', 'description' => '...',
//  'due' => 'YYYY-MM-DD', 'status' => 'todo|inprogress|completed',
//  'done' => bool, 'important' => bool, 'deleted' => bool]
// (due, status, done, important, deleted are optional in the template; missing = empty/false/todo)

// View + status filter used by the template (sidebar, tabs, trash buttons).
$view         = $_GET['view'] ?? 'mytasks';
$statusFilter = $_GET['status'] ?? 'all';
// $pageTitle is defined separately (e.g. "My Tasks", "Completed", "Important", "Trash").

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
          <li class="nav-item<?= $view === 'mytasks' ? ' active' : '' ?>"><a href="?view=mytasks"<?= $view === 'mytasks' ? ' aria-current="page"' : '' ?>><img src="assets/tasks.png" alt="" class="nav-icon"><span>My Tasks</span></a></li>
          <li class="nav-item<?= $view === 'completed' ? ' active' : '' ?>"><a href="?view=completed"<?= $view === 'completed' ? ' aria-current="page"' : '' ?>><img src="assets/completed.png" alt="" class="nav-icon"><span>Completed</span></a></li>
          <li class="nav-item<?= $view === 'important' ? ' active' : '' ?>"><a href="?view=important"<?= $view === 'important' ? ' aria-current="page"' : '' ?>><img src="assets/Brown Star Outline.png" alt="" class="nav-icon"><span>Important</span></a></li>
          <li class="nav-item<?= $view === 'trash' ? ' active' : '' ?>"><a href="?view=trash"<?= $view === 'trash' ? ' aria-current="page"' : '' ?>><img src="assets/trash.png" alt="" class="nav-icon"><span>Trash</span></a></li>
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
        <h1 class="page-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>

        <?php if ($view === 'trash'): ?>
          <form method="POST" class="empty-trash-form">
            <input type="hidden" name="action" value="empty_trash">
            <button type="submit" class="btn-danger">Empty Trash</button>
          </form>
        <?php else: ?>
          <button type="button" class="add-task-btn" id="add-task-btn" aria-controls="add-task-section" aria-expanded="<?= empty($errors) ? 'false' : 'true' ?>">+ Add Task</button>
        <?php endif; ?>
      </header>

      <?php if ($view === 'mytasks'): ?>
        <!-- Status tabs -->
        <nav class="tabs" aria-label="Filter tasks by status">
          <ul>
            <li><a href="?view=mytasks" class="tab<?= $statusFilter === 'all' ? ' active' : '' ?>"<?= $statusFilter === 'all' ? ' aria-current="page"' : '' ?>>All</a></li>
            <li><a href="?view=mytasks&amp;status=todo" class="tab<?= $statusFilter === 'todo' ? ' active' : '' ?>"<?= $statusFilter === 'todo' ? ' aria-current="page"' : '' ?>>To Do</a></li>
            <li><a href="?view=mytasks&amp;status=inprogress" class="tab<?= $statusFilter === 'inprogress' ? ' active' : '' ?>"<?= $statusFilter === 'inprogress' ? ' aria-current="page"' : '' ?>>In Progress</a></li>
          </ul>
        </nav>
      <?php endif; ?>

      <?php if ($view !== 'trash'): ?>
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

            <label for="task_due">Due Date</label>
            <input type="date" id="task_due" name="task_due">

            <label for="task_status">Status</label>
            <select id="task_status" name="task_status">
              <option value="todo" selected>To Do</option>
              <option value="inprogress">In Progress</option>
              <option value="completed">Completed</option>
            </select>

            <button type="submit" name="submit" class="save-btn">Add Task</button>
          </form>
        </section>
      <?php endif; ?>

      <!-- Task list -->
      <section class="tasks" aria-labelledby="task-list-title">
        <h2 class="section-title" id="task-list-title"><?= $view === 'trash' ? 'Deleted Tasks' : 'Your Tasks' ?></h2>

        <?php if (empty($_SESSION['tasks'])): ?>
          <?php if ($view === 'trash'): ?>
            <p class="empty-state">Trash is empty.</p>
          <?php else: ?>
            <p class="empty-state">No tasks yet. Click "+ Add Task" to create one.</p>
          <?php endif; ?>
        <?php endif; ?>

        <ul class="task-list" id="task-list">
          <?php foreach ($_SESSION['tasks'] as $task):
            $id          = htmlspecialchars($task['id'], ENT_QUOTES, 'UTF-8');
            $name        = htmlspecialchars($task['name'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($task['description'], ENT_QUOTES, 'UTF-8');
            $dueRaw      = $task['due'] ?? '';
            $due         = htmlspecialchars($dueRaw, ENT_QUOTES, 'UTF-8');
            $dueLabel    = $dueRaw !== '' ? date('M d, Y', strtotime($dueRaw)) : '';
            $taskStatus  = $task['status'] ?? 'todo';
            $isDone      = !empty($task['done']);
            $isImportant = !empty($task['important']);
          ?>
            <li class="task-card<?= $isDone ? ' completed' : '' ?>">

              <!-- Read view -->
              <div class="task-view">

                <?php if ($view !== 'trash'): ?>
                  <!-- Complete toggle -->
                  <form method="POST" class="toggle-form">
                    <input type="hidden" name="action" value="toggle_done">
                    <input type="hidden" name="task_id" value="<?= $id ?>">
                    <label for="done_<?= $id ?>" class="sr-only">Mark "<?= $name ?>" as done</label>
                    <input type="checkbox" id="done_<?= $id ?>" class="task-check" <?= $isDone ? 'checked' : '' ?> onchange="this.form.submit()">
                    <noscript><button type="submit">Update</button></noscript>
                  </form>
                <?php endif; ?>

                <div class="task-info">
                  <h3 class="task-title"><?= $name ?></h3>
                  <p class="task-desc"><?= $description ?></p>
                </div>

                <div class="task-meta">
                  <?php if ($dueLabel !== ''): ?>
                    <span class="task-date"><time datetime="<?= $due ?>"><?= $dueLabel ?></time></span>
                  <?php endif; ?>

                  <?php if ($view === 'trash'): ?>
                    <!-- Trash actions -->
                    <form method="POST" class="restore-form">
                      <input type="hidden" name="action" value="restore">
                      <input type="hidden" name="task_id" value="<?= $id ?>">
                      <button type="submit" class="icon-btn restore">Restore</button>
                    </form>
                    <form method="POST" class="delete-forever-form">
                      <input type="hidden" name="action" value="delete_forever">
                      <input type="hidden" name="task_id" value="<?= $id ?>">
                      <button type="submit" class="icon-btn delete">Delete permanently</button>
                    </form>
                  <?php else: ?>
                    <!-- Important toggle -->
                    <form method="POST" class="star-form">
                      <input type="hidden" name="action" value="toggle_star">
                      <input type="hidden" name="task_id" value="<?= $id ?>">
                      <button type="submit" class="icon-btn star<?= $isImportant ? ' active' : '' ?>" aria-pressed="<?= $isImportant ? 'true' : 'false' ?>" aria-label="Mark &quot;<?= $name ?>&quot; as important"><?= $isImportant ? '&#9733;' : '&#9734;' ?></button>
                    </form>

                    <button type="button" class="icon-btn edit">Edit</button>

                    <!-- Delete (moves the task to Trash) -->
                    <form method="POST" class="delete-form">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="task_id" value="<?= $id ?>">
                      <button type="submit" class="icon-btn delete">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>

              <?php if ($view !== 'trash'): ?>
                <!-- Inline edit form (hidden until Edit is clicked) -->
                <form method="POST" class="edit-form" hidden>
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="task_id" value="<?= $id ?>">

                  <label for="edit_name_<?= $id ?>">Task Name</label>
                  <input type="text" id="edit_name_<?= $id ?>" name="task_name" value="<?= $name ?>" required>

                  <label for="edit_description_<?= $id ?>">Task Description</label>
                  <input type="text" id="edit_description_<?= $id ?>" name="task_description" value="<?= $description ?>" required>

                  <label for="edit_due_<?= $id ?>">Due Date</label>
                  <input type="date" id="edit_due_<?= $id ?>" name="task_due" value="<?= $due ?>">

                  <label for="edit_status_<?= $id ?>">Status</label>
                  <select id="edit_status_<?= $id ?>" name="task_status">
                    <option value="todo"<?= $taskStatus === 'todo' ? ' selected' : '' ?>>To Do</option>
                    <option value="inprogress"<?= $taskStatus === 'inprogress' ? ' selected' : '' ?>>In Progress</option>
                    <option value="completed"<?= $taskStatus === 'completed' ? ' selected' : '' ?>>Completed</option>
                  </select>

                  <button type="submit" class="btn-save">Save</button>
                  <button type="button" class="btn-cancel">Cancel</button>
                </form>
              <?php endif; ?>

            </li>
          <?php endforeach; ?>
        </ul>
      </section>
    </main>

  </div>

  <script src="js/script.js"></script>
</body>
</html>