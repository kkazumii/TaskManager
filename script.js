/* =========================================================
   TaskFlow — script.js  

   EVENTS USED:
     1. input    -> live search filtering
     2. click    -> open/close Add Task form, toggle Edit forms,
                    star a task, delete a task, filter by status
     3. change   -> checkbox marks a task done/undone
     4. keydown  -> Escape closes open forms
   ========================================================= */

/* short helpers for selecting elements */
const $ = (selector, parent = document) => parent.querySelector(selector);
const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];

document.addEventListener("DOMContentLoaded", function () {

  const taskList = $("#task-list");

  /* ---------- 1. Live Search Filtering ---------- */

  /* FUNCTION 1 — searchTasks()                     [input] */
  function searchTasks(event) {
    const filter = event.target.value.trim().toLowerCase();
    let visibleCount = 0;

    $$(".task-item").forEach(function (task) {
      const text = $(".task-details", task).textContent.toLowerCase();
      const isMatch = text.includes(filter);
      task.style.display = isMatch ? "" : "none";
      if (isMatch) {
        visibleCount++;
      }
    });

    // Show a message when no task matches the search
    $("#no-results").hidden = visibleCount > 0;
  }
  $("#task-search").addEventListener("input", searchTasks);

  /* ---------- 2. Add Task Form ---------- */
  const addTaskBtn = $("#add-task-btn");
  const addTaskSection = $("#add-task-section");

  /* FUNCTION 2 — toggleAddForm()                   [click] */
  function toggleAddForm() {
    const willOpen = addTaskSection.hidden;
    addTaskSection.hidden = !willOpen;
    addTaskBtn.setAttribute("aria-expanded", willOpen);
  }
  addTaskBtn.addEventListener("click", toggleAddForm);

  /* FUNCTION 3 — closeAddForm()                    [click] */
  function closeAddForm() {
    addTaskSection.hidden = true;
    addTaskBtn.setAttribute("aria-expanded", "false");
  }
  $("#cancel-task-btn").addEventListener("click", closeAddForm);

  /* ---------- 3. Inline Edit Forms ---------- */

  /* FUNCTION 4 — toggleEditForm()                  [click] */
  function toggleEditForm(event) {
    const editBtn = event.target.closest(".btn-icon.edit");
    if (!editBtn) {
      return;
    }
    const editForm = $(".edit-form", editBtn.closest(".task-item"));
    editForm.hidden = !editForm.hidden;
    editBtn.setAttribute("aria-expanded", !editForm.hidden);
  }
  taskList.addEventListener("click", toggleEditForm);

  /* FUNCTION 5 — closeEditForm()                   [click] */
  function closeEditForm(event) {
    const cancelBtn = event.target.closest(".edit-form .btn-secondary");
    if (!cancelBtn) {
      return;
    }
    const taskItem = cancelBtn.closest(".task-item");
    $(".edit-form", taskItem).hidden = true;
    $(".btn-icon.edit", taskItem).setAttribute("aria-expanded", "false");
  }
  taskList.addEventListener("click", closeEditForm);

  /* ---------- 4. Star + Mark Done ---------- */

  /* FUNCTION 6 — toggleStar()                      [click] */
  function toggleStar(event) {
    const starBtn = event.target.closest(".btn-icon.star");
    if (!starBtn) {
      return;
    }
    const isActive = starBtn.classList.toggle("is-active");
    starBtn.textContent = isActive ? "★" : "☆";
    starBtn.setAttribute("aria-pressed", isActive);
  }
  taskList.addEventListener("click", toggleStar);

  /* FUNCTION 7 — toggleDone()                      [change] */
  function toggleDone(event) {
    if (!event.target.classList.contains("task-checkbox")) {
      return;
    }
    const taskItem = event.target.closest(".task-item");
    taskItem.classList.toggle("is-complete", event.target.checked);
    $("h3", taskItem).classList.toggle("is-done", event.target.checked);
  }
  taskList.addEventListener("change", toggleDone);

    /* ---------- 5. Delete Confirmation (modal) ---------- */
  const deleteModal = $("#delete-modal");
  let taskToDelete = null;

    /* FUNCTION 8 — openDeleteModal()                 [click] */
    function openDeleteModal(event) {
    const deleteBtn = event.target.closest(".btn-icon.delete");
    if (!deleteBtn) {
      return;
    }
    taskToDelete = deleteBtn.closest(".task-item");
    $("#delete-modal-name").textContent = $("h3", taskToDelete).textContent;
    deleteModal.showModal();
  }
  taskList.addEventListener("click", openDeleteModal);

  /* FUNCTION 9 — confirmDelete()                   [click] */
  function confirmDelete() {
    if (taskToDelete) {
      taskToDelete.remove();
      taskToDelete = null;
    }
    deleteModal.close();
  }
  $("#delete-confirm-btn").addEventListener("click", confirmDelete);

  /* FUNCTION 10 — closeDeleteModal()               [click] */
  function closeDeleteModal(event) {
    if (event.target.id === "delete-cancel-btn" || event.target === deleteModal) {
      taskToDelete = null;
      deleteModal.close();
    }
  }
  $("#delete-cancel-btn").addEventListener("click", closeDeleteModal);
  deleteModal.addEventListener("click", closeDeleteModal);

  /* ---------- 6. Status Filter Tabs ---------- */

  /* FUNCTION 11 — filterByStatus()                  [click] */
  function filterByStatus(event) {
    event.preventDefault();
    const status = this.dataset.status;

    $$(".filter-tab").forEach(function (tab) {
      tab.classList.toggle("active", tab === event.currentTarget);
    });
    $$(".task-item").forEach(function (task) {
      task.hidden = status !== "all" && task.dataset.status !== status;
    });
  }
  $$(".filter-tab").forEach(function (tab) {
    tab.addEventListener("click", filterByStatus);
  });

  /* ---------- 7. Keyboard Shortcut ---------- */

  /* FUNCTION 12 — closeFormsOnEscape()             [keydown] */
  function closeFormsOnEscape(event) {
    if (event.key !== "Escape") {
      return;
    }
    closeAddForm();
    $$(".edit-form").forEach(function (form) {
      form.hidden = true;
    });
    $$(".btn-icon.edit").forEach(function (btn) {
      btn.setAttribute("aria-expanded", "false");
    });
  }
  document.addEventListener("keydown", closeFormsOnEscape);

});
