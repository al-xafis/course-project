document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".add_item_link").forEach((btn) => {
    btn.addEventListener("click", addFormToCollection);
  });

  document.querySelectorAll(".tags .item").forEach((tag) => {
    addTagFormDeleteLink(tag);
  });

  function addFormToCollection(e) {
    const collectionHolder = document.querySelector(".tags");

    const item = document.createElement("div");

    item.innerHTML = collectionHolder.dataset.prototype.replace(
      /__name__/g,
      collectionHolder.dataset.index
    );

    collectionHolder.appendChild(item);
    collectionHolder.dataset.index++;

    addTagFormDeleteLink(item);
  }

  function addTagFormDeleteLink(item) {
    const removeFormButton = document.createElement("button");
    removeFormButton.className = "btn btn-danger mb-3";
    removeFormButton.innerText = "Delete a tag";

    item.append(removeFormButton);

    removeFormButton.addEventListener("click", (e) => {
      e.preventDefault();
      // remove the li for the tag form
      item.remove();
    });
  }
});
