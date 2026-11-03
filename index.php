<?php
$pageTitle = "Task Manager";
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
<!--------- HTML STRUCTURE --------------->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Tasks | TaskFlow</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!--------- LEFT SIDEBAR --------------->
<aside class="sidebar">
  <div class="logo">
    <img src="assets/logo.png" alt="" class="logo-img">
    <div class="logo-text">
      <span class="logo-title">TaskFlow</span>
      <span class="logo-sub">Small steps. Big progress.</span>
    </div>
  </div>

  <nav aria-label="Task categories">
    <ul class="nav-menu">
      <li>
        <a href="#" class="nav-item active" aria-current="page">
          <img src="assets/tasks.png" alt="" class="nav-icon">
          <span>My Tasks</span>
          <span class="nav-count">3</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-item">
          <img src="assets/completed.png" alt="" class="nav-icon">
          <span>Completed</span>
          <span class="nav-count">1</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-item">
          <img src="assets/Brown Star Outline.png" alt="" class="nav-icon">
          <span>Important</span>
          <span class="nav-count">1</span>
        </a>
      </li>
      <li>
        <a href="#" class="nav-item">
          <img src="assets/trash.png" alt="" class="nav-icon">
          <span>Trash</span>
          <span class="nav-count">0</span>
        </a>
      </li>
    </ul>
  </nav>

  <footer class="user">
    <img src="assets/taskflow_user_icon.png" alt="User avatar" class="user-avatar">
    <span class="user-name">ZenaidaInk</span>
    <img src="assets/taskflow_dropdown_icon.png" alt="" class="user-arrow">
  </footer>
</aside>

<!--------- MAIN CONTENT --------------->
<main class="main-content">

  <header class="app-header">
    <div class="header-title">
      <h1>My Tasks</h1>
      <p>What's on the agenda today?</p>
    </div>
    <div class="header-actions">
      <div class="search-bar">
        <img src="assets/Minimal Taupe Magnifying Glass Icon.png" alt="">
        <input type="text" id="task-search" placeholder="Search tasks..." aria-label="Search tasks">
      </div>
      <button type="button" class="btn-primary" id="add-task-btn" aria-controls="add-task-section" aria-expanded="false">
        <img src="assets/add.png" alt=""> Add Task
      </button>
    </div>
  </header>

  <nav class="filter-tabs" aria-label="Filter tasks by status">
    <span class="filter-label"><img src="assets/filter.png" alt=""> Filter:</span>
    <a href="#" class="filter-tab active" data-status="all">All</a>
    <a href="#" class="filter-tab" data-status="todo">Pending</a>
    <a href="#" class="filter-tab" data-status="inprogress">In Progress</a>
    <a href="#" class="filter-tab" data-status="completed">Completed</a>
  </nav>

  <!--------- ADD TASK FORM --------------->
  <section class="add-task-section" id="add-task-section" aria-labelledby="add-task-title" hidden>
    <h2 id="add-task-title">Add new task</h2>
    <form class="task-form">
      <div class="field"><label for="task_name">Title</label><input type="text" id="task_name" placeholder="e.g. Finish Chapter 2" maxlength="100"></div>
      <div class="field"><label for="task_description">Description</label><input type="text" id="task_description" placeholder="Add a short description" maxlength="255"></div>
      <div class="form-row">
        <div class="field"><label for="task_due">Due date</label><input type="date" id="task_due"></div>
        <div class="field"><label for="task_status">Status</label>
          <select id="task_status"><option>Pending</option><option>In Progress</option><option>Completed</option></select>
        </div>
      </div>
      <div class="form-actions">
        <button type="button" class="btn-primary btn-secondary" id="cancel-task-btn">Cancel</button>
        <button type="submit" class="btn-primary">Create task</button>
      </div>
    </form>
  </section>

  <!--------- TASK LIST --------------->
  <section class="tasks" aria-label="Task list">
    <p class="empty-state" id="no-results" hidden>No tasks match your search.</p>
    <ul class="task-list" id="task-list">
    
    <!-- ----------------------  note for backend  !! ---------------------------------------------------------------->
    <!-- 
           These 3 tasks are just samples.
           Delete them and loop the real tasks instead, one <li class="task-item"> each.
           Just keep the same classes para d masira design
           Things to fill in per task: status, name, description, due date,
           star (is-active), and the values inside the edit form. -->

      <li class="task-item" data-status="inprogress">
        <div class="task-row">
          <input type="checkbox" class="task-checkbox" aria-label="Mark &quot;Finish Chapter 1&quot; as done">
          <div class="task-details">
            <h3>Finish Chapter 1</h3>
            <p>Identify the Scope and Delimitations</p>
          </div>
          <span class="status-badge status-progress">In Progress</span>
          <span class="task-date"><img src="assets/calendar.png" alt="">Nov 07, 2026</span>
          <div class="task-actions">
            <button type="button" class="btn-icon star is-active" aria-label="Mark as important" aria-pressed="true">★</button>
            <button type="button" class="btn-icon edit" aria-label="Edit &quot;Finish Chapter 1&quot;" aria-expanded="false"><img src="assets/edit.png" alt=""></button>
            <button type="button" class="btn-icon delete" aria-label="Move &quot;Finish Chapter 1&quot; to trash"><img src="assets/trash.png" alt=""></button>
          </div>
        </div>
        <form class="task-form edit-form" hidden>
          <div class="field"><label for="edit_name_1">Title</label><input type="text" id="edit_name_1" value="Finish Chapter 1"></div>
          <div class="field"><label for="edit_desc_1">Description</label><input type="text" id="edit_desc_1" value="Identify the Scope and Delimitations"></div>
          <div class="form-row">
            <div class="field"><label for="edit_due_1">Due date</label><input type="date" id="edit_due_1"></div>
            <div class="field"><label for="edit_status_1">Status</label>
              <select id="edit_status_1">
                <option>Pending</option>
                <option selected>In Progress</option>
                <option>Completed</option>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-primary btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save changes</button>
          </div>
        </form>
      </li>
      <li class="task-item" data-status="todo">
        <div class="task-row">
          <input type="checkbox" class="task-checkbox" aria-label="Mark &quot;Buy groceries&quot; as done">
          <div class="task-details">
            <h3>Buy groceries</h3>
            <p>Milk, eggs, bread, and butter</p>
          </div>
          <span class="status-badge status-pending">Pending</span>
          <span class="task-date is-overdue"><img src="assets/calendar.png" alt="">Nov 03, 2026 (overdue)</span>
          <div class="task-actions">
            <button type="button" class="btn-icon star" aria-label="Mark as important" aria-pressed="false">☆</button>
            <button type="button" class="btn-icon edit" aria-label="Edit &quot;Buy groceries&quot;" aria-expanded="false"><img src="assets/edit.png" alt=""></button>
            <button type="button" class="btn-icon delete" aria-label="Move &quot;Buy groceries&quot; to trash"><img src="assets/trash.png" alt=""></button>
          </div>
        </div>
        <form class="task-form edit-form" hidden>
          <div class="field"><label for="edit_name_2">Title</label><input type="text" id="edit_name_2" value="Buy groceries"></div>
          <div class="field"><label for="edit_desc_2">Description</label><input type="text" id="edit_desc_2" value="Milk, eggs, bread, and butter"></div>
          <div class="form-row">
            <div class="field"><label for="edit_due_2">Due date</label><input type="date" id="edit_due_2"></div>
            <div class="field"><label for="edit_status_2">Status</label>
              <select id="edit_status_2">
                <option selected>Pending</option>
                <option>In Progress</option>
                <option>Completed</option>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-primary btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save changes</button>
          </div>
        </form>
      </li>
      <li class="task-item is-complete" data-status="completed">
        <div class="task-row">
          <input type="checkbox" class="task-checkbox" aria-label="Mark &quot;Read a Book&quot; as done" checked>
          <div class="task-details">
            <h3 class="is-done">Read a Book</h3>
            <p>Finish 2 chapters of Atomic Habits</p>
          </div>
          <span class="status-badge status-completed">Completed</span>
          <span class="task-date"><img src="assets/calendar.png" alt="">No due date</span>
          <div class="task-actions">
            <button type="button" class="btn-icon star" aria-label="Mark as important" aria-pressed="false">☆</button>
            <button type="button" class="btn-icon edit" aria-label="Edit &quot;Read a Book&quot;" aria-expanded="false"><img src="assets/edit.png" alt=""></button>
            <button type="button" class="btn-icon delete" aria-label="Move &quot;Read a Book&quot; to trash"><img src="assets/trash.png" alt=""></button>
          </div>
        </div>
        <form class="task-form edit-form" hidden>
          <div class="field"><label for="edit_name_3">Title</label><input type="text" id="edit_name_3" value="Read a Book"></div>
          <div class="field"><label for="edit_desc_3">Description</label><input type="text" id="edit_desc_3" value="Finish 2 chapters of Atomic Habits"></div>
          <div class="form-row">
            <div class="field"><label for="edit_due_3">Due date</label><input type="date" id="edit_due_3"></div>
            <div class="field"><label for="edit_status_3">Status</label>
              <select id="edit_status_3">
                <option>Pending</option>
                <option>In Progress</option>
                <option selected>Completed</option>
              </select>
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn-primary btn-secondary">Cancel</button>
            <button type="submit" class="btn-primary">Save changes</button>
          </div>
        </form>
      </li>
      <!-- ----------------------  end of sample tasks  !! ---------------------------------------------------------------->
    </ul>
  </section>
</main>

<!--------- RIGHT SIDE BAR --------------->
<aside class="right-sidebar" aria-label="Task overview">
  <h2>Overview</h2>
  <div class="progress-card">
    <p class="progress-value">33%</p>
    <p class="progress-caption">of your tasks are done</p>
    <div class="progress-track" role="progressbar" aria-label="Completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="33">
      <div class="progress-fill" style="width: 33%"></div>
    </div>
    <p class="progress-sub">1 of 3 tasks</p>
  </div>
  <ul class="stat-list">
    <li><span class="dot dot-pending"></span><span>Pending</span><strong>1</strong></li>
    <li><span class="dot dot-progress"></span><span>In progress</span><strong>1</strong></li>
    <li><span class="dot dot-completed"></span><span>Completed</span><strong>1</strong></li>
    <li><span class="dot dot-overdue"></span><span>Overdue</span><strong>1</strong></li>
  </ul>
</aside>

<!-- Delete modal (script.js) -->
<dialog class="modal" id="delete-modal" aria-labelledby="delete-modal-title">
  <h2 id="delete-modal-title">Move to Trash?</h2>
  <p>"<span id="delete-modal-name">this task</span>" will be moved to Trash. You can restore it later.</p>
  <div class="form-actions">
    <button type="button" class="btn-primary btn-secondary" id="delete-cancel-btn">Cancel</button>
    <button type="button" class="btn-primary btn-danger" id="delete-confirm-btn">Move to Trash</button>
  </div>
</dialog>

<script src="script.js"></script>
</body>
</html>