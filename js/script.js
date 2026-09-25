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
  const successToast = document.querySelector(".success-toast");

  if (successToast) {
    window.setTimeout(function () {
      successToast.classList.add("is-hidden");

      window.setTimeout(function () {
        successToast.remove();
      }, 300);
    }, 5000);
  }

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

  const dateDividerButtons = document.querySelectorAll(".date-divider-button");
  const applicantTableBody = document.querySelector(
    "tbody[data-search-active]",
  );
  const searchIsActive = applicantTableBody
    ? applicantTableBody.getAttribute("data-search-active") === "true"
    : false;

  dateDividerButtons.forEach(function (dateDividerButton) {
    const dateDividerRow = dateDividerButton.closest(".date-divider-row");
    const groupId = dateDividerButton.getAttribute("data-date-toggle");
    const groupRows = document.querySelectorAll(
      '[data-date-group="' + groupId + '"]',
    );
    const paginationRow = document.querySelector(
      '[data-pagination-for="' + groupId + '"]',
    );
    const pagination = paginationRow
      ? paginationRow.querySelector(".date-pagination")
      : null;
    const pageSize = 20;
    const pageCount = Math.ceil(groupRows.length / pageSize);

    function showPage(page) {
      groupRows.forEach(function (groupRow, index) {
        const isOnPage =
          index >= (page - 1) * pageSize && index < page * pageSize;
        groupRow.hidden = !isOnPage;
      });

      if (pagination) {
        pagination
          .querySelectorAll(".date-page-button")
          .forEach(function (button) {
            const isActive = Number(button.getAttribute("data-page")) === page;
            button.classList.toggle("is-active", isActive);
            button.setAttribute("aria-current", isActive ? "page" : "false");
          });
      }
    }

    if (pagination) {
      for (let page = 1; page <= pageCount; page += 1) {
        const pageButton = document.createElement("button");
        pageButton.type = "button";
        pageButton.className = "date-page-button";
        pageButton.textContent = String(page);
        pageButton.setAttribute("data-page", String(page));
        pageButton.setAttribute("aria-label", "Show page " + page);
        pageButton.addEventListener("click", function () {
          showPage(page);
        });
        pagination.appendChild(pageButton);
      }
    }

    function toggleDateGroup() {
      const isOpening =
        dateDividerButton.getAttribute("aria-expanded") !== "true";
      dateDividerButton.setAttribute("aria-expanded", String(isOpening));
      dateDividerButton.querySelector(".date-divider-icon").textContent =
        isOpening ? "-" : "+";

      if (isOpening) {
        showPage(1);
        if (paginationRow) {
          paginationRow.hidden = false;
        }
      } else {
        groupRows.forEach(function (groupRow) {
          groupRow.hidden = true;
        });
        if (paginationRow) {
          paginationRow.hidden = true;
        }
      }
    }

    dateDividerRow.addEventListener("click", toggleDateGroup);

    if (searchIsActive) {
      dateDividerButton.setAttribute("aria-expanded", "true");
      dateDividerButton.querySelector(".date-divider-icon").textContent = "-";
      showPage(1);

      if (paginationRow) {
        paginationRow.hidden = false;
      }
    }
  });

  const deleteModal = document.getElementById("deleteConfirmationModal");
  const confirmDeleteButton = document.getElementById("confirmDeleteButton");
  const cancelDeleteButton = document.getElementById("cancelDeleteButton");
  const deleteModalTitle = document.getElementById("deleteModalTitle");
  const deleteModalMessage = document.getElementById("deleteModalMessage");
  const batchDeleteButton = document.getElementById("batchDeleteButton");
  const confirmBatchDeleteButton = document.getElementById(
    "confirmBatchDeleteButton",
  );
  const batchDeleteForm = document.getElementById("batchDeleteForm");
  const selectedApplicantCount = document.getElementById(
    "selectedApplicantCount",
  );
  const applicantCheckboxes = document.querySelectorAll(".applicant-checkbox");
  const selectAllApplicants = document.getElementById("selectAllApplicants");
  const dashboardBody = document.body;

  if (!deleteModal || !confirmDeleteButton || !cancelDeleteButton) {
    return;
  }

  if (!batchDeleteButton || !confirmBatchDeleteButton || !batchDeleteForm) {
    return;
  }

  function closeDeleteModal() {
    deleteModal.style.display = "none";
    confirmDeleteButton.setAttribute("href", "#");
    confirmDeleteButton.removeAttribute("data-batch-delete");
    deleteModalTitle.textContent = "Delete applicant?";
    deleteModalMessage.textContent =
      "This action permanently removes this applicant from the records.";
    confirmDeleteButton.textContent = "Delete applicant";
  }

  function updateSelectionState() {
    const selectedCount = document.querySelectorAll(
      ".applicant-checkbox:checked",
    ).length;
    selectedApplicantCount.textContent = String(selectedCount);
    confirmBatchDeleteButton.disabled = selectedCount === 0;

    if (selectAllApplicants) {
      selectAllApplicants.checked =
        applicantCheckboxes.length > 0 &&
        selectedCount === applicantCheckboxes.length;
      selectAllApplicants.indeterminate =
        selectedCount > 0 && selectedCount < applicantCheckboxes.length;
    }
  }

  applicantCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener("change", updateSelectionState);
  });

  if (selectAllApplicants) {
    selectAllApplicants.addEventListener("change", function () {
      applicantCheckboxes.forEach(function (checkbox) {
        checkbox.checked = selectAllApplicants.checked;
      });
      updateSelectionState();
    });
  }

  function setBatchSelectionMode(isActive) {
    dashboardBody.classList.toggle("batch-selection-hidden", !isActive);
    batchDeleteButton.setAttribute("aria-pressed", String(isActive));
    batchDeleteButton.textContent = isActive
      ? "Cancel Batch Delete"
      : "Batch Delete";
    confirmBatchDeleteButton.hidden = !isActive;

    if (isActive) {
      dateDividerButtons.forEach(function (dateDividerButton) {
        if (dateDividerButton.getAttribute("aria-expanded") !== "true") {
          dateDividerButton.closest(".date-divider-row").click();
        }
      });
    } else {
      applicantCheckboxes.forEach(function (checkbox) {
        checkbox.checked = false;
      });
      updateSelectionState();
    }
  }

  batchDeleteButton.addEventListener("click", function () {
    const isActive = batchDeleteButton.getAttribute("aria-pressed") === "true";

    setBatchSelectionMode(!isActive);
  });

  confirmBatchDeleteButton.addEventListener("click", function () {
    const selectedCount = document.querySelectorAll(
      ".applicant-checkbox:checked",
    ).length;

    if (selectedCount === 0) {
      return;
    }

    deleteModalTitle.textContent = "Delete selected applicants?";
    deleteModalMessage.textContent =
      "This action permanently removes " +
      selectedCount +
      " selected applicants from the records.";
    confirmDeleteButton.textContent = "Delete selected";
    confirmDeleteButton.setAttribute("data-batch-delete", "true");
    deleteModal.style.display = "flex";
    cancelDeleteButton.focus();
  });

  document
    .querySelectorAll(".delete-trigger")
    .forEach(function (deleteTrigger) {
      deleteTrigger.addEventListener("click", function (event) {
        event.preventDefault();
        confirmDeleteButton.removeAttribute("data-batch-delete");
        confirmDeleteButton.setAttribute(
          "href",
          deleteTrigger.getAttribute("data-delete-url"),
        );
        deleteModal.style.display = "flex";
        cancelDeleteButton.focus();
      });
    });

  cancelDeleteButton.addEventListener("click", closeDeleteModal);

  confirmDeleteButton.addEventListener("click", function (event) {
    if (confirmDeleteButton.getAttribute("data-batch-delete") === "true") {
      event.preventDefault();
      batchDeleteForm.submit();
    }
  });

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
