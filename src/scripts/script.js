$(document).ready(function () {
  $(".edit-btn").on("click", function () {
    var $task = $(this).closest(".task");
    $task.find(".status").addClass("hidden");
    $task.find(".task-desc").addClass("hidden");
    $task.find(".task-act").addClass("hidden");
    $task.find(".task-date").addClass("hidden");
    $task.find(".edit-task").removeClass("hidden");
  });

  $(".status").on("click", function () {
    if ($(this).is(":checked")) {
      $(this).addClass("done");
    } else {
      $(this).removeClass("done");
    }
  });
});
