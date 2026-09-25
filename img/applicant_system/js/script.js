function showConfirmation() {
  const form = document.getElementById("applicantForm");

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }

  document.getElementById("confirmationModal").style.display = "flex";
}

function closeConfirmation() {
  document.getElementById("confirmationModal").style.display = "none";
}

function submitForm() {
  const form = document.getElementById("applicantForm");

  closeConfirmation();

  if (typeof form.requestSubmit === "function") {
    form.requestSubmit();
    return;
  }

  form.submit();
}

function placeMobileCursor(input) {
  if (input.value === "09") {
    input.setSelectionRange(input.value.length, input.value.length);
  }
}

function enforceMobilePrefix(input) {
  let digits = input.value.replace(/\D/g, "");

  if (digits.startsWith("09")) {
    digits = digits.slice(2);
  } else {
    digits = digits.replace(/^[09]+/, "");
  }

  input.value = `09${digits.slice(0, 9)}`;
}

function completeGmailAddress(input) {
  if (input.value.endsWith("@")) {
    input.value += "gmail.com";
    input.setSelectionRange(input.value.length, input.value.length);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const fullscreenButton = document.getElementById("fullscreenButton");

  if (fullscreenButton) {
    let shortcutSpacePressed = false;

    function updateFullscreenButton() {
      const isFullscreen = Boolean(document.fullscreenElement);
      const label = isFullscreen ? "Exit fullscreen" : "Enter fullscreen";

      fullscreenButton.textContent = isFullscreen
        ? "Exit fullscreen"
        : "Fullscreen";
      fullscreenButton.setAttribute("aria-label", label);
      fullscreenButton.setAttribute("title", label);
    }

    function toggleFullscreen() {
      if (!document.fullscreenElement) {
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        }
      } else if (document.exitFullscreen) {
        document.exitFullscreen();
      }
    }

    document.addEventListener("fullscreenchange", updateFullscreenButton);
    updateFullscreenButton();

    document.addEventListener("keydown", function (event) {
      if (event.ctrlKey && event.altKey && event.code === "Space") {
        shortcutSpacePressed = true;
        event.preventDefault();
        return;
      }

      if (
        event.ctrlKey &&
        event.altKey &&
        shortcutSpacePressed &&
        event.key === "Enter"
      ) {
        event.preventDefault();
        toggleFullscreen();
      }
    });

    document.addEventListener("keyup", function (event) {
      if (event.code === "Space") {
        shortcutSpacePressed = false;
      }
    });
  }

  const sidebarToggle = document.getElementById("sidebarToggle");

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", function () {
      const sidebarIsHidden = document.body.classList.toggle("sidebar-hidden");
      const label = sidebarIsHidden ? "Show sidebar" : "Hide sidebar";

      sidebarToggle.setAttribute("aria-expanded", String(!sidebarIsHidden));
      sidebarToggle.setAttribute("aria-label", label);
      sidebarToggle.setAttribute("title", label);
    });
  }

  const deleteModal = document.getElementById("deleteConfirmationModal");
  const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  const cancelDeleteButton = document.getElementById("cancelDeleteButton");

  if (!deleteModal || !confirmDeleteButton || !cancelDeleteButton) {
    return;
  }

  function closeDeleteModal() {
    deleteModal.style.display = "none";
    confirmDeleteButton.setAttribute("href", "#");
  }

  document
    .querySelectorAll(".delete-trigger")
    .forEach(function (deleteTrigger) {
      deleteTrigger.addEventListener("click", function (event) {
        event.preventDefault();
        confirmDeleteButton.setAttribute(
          "href",
          deleteTrigger.getAttribute("data-delete-url"),
        );
        deleteModal.style.display = "flex";
        cancelDeleteButton.focus();
      });
    });

  cancelDeleteButton.addEventListener("click", closeDeleteModal);

  deleteModal.addEventListener("click", function (event) {
    if (event.target === deleteModal) {
      closeDeleteModal();
    }
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && deleteModal.style.display === "flex") {
      closeDeleteModal();
    }
  });
});
