document.addEventListener("DOMContentLoaded", () => {
  let flashContainer = document.querySelector(".flash-messages");
  if (flashContainer) {
    setTimeout(() => {
      flashContainer.remove();
    }, 6000);
  }
});
