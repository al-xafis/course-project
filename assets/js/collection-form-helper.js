// document.addEventListener("DOMContentLoaded", () => {
//   document.querySelectorAll(".add_item_link").forEach((btn) => {
//     btn.addEventListener("click", addFormToCollection);
//   });

//   document
//     .querySelectorAll(".custom-attributes-wrapper .item")
//     .forEach((row) => {
//       addRemoveFormAttribute(row);
//     });
// });

// function addFormToCollection(e) {
//   const collectionHolder = document.querySelector(".custom-attributes-wrapper");

//   const item = document.createElement("div");
//   item.className = "item";

//   item.innerHTML = collectionHolder.dataset.prototype.replace(
//     /__name__/g,
//     collectionHolder.dataset.index
//   );

//   collectionHolder.appendChild(item);

//   collectionHolder.dataset.index++;

//   addRemoveFormAttribute(item);
// }

// function addRemoveFormAttribute(item) {
//   const removeFormButton = document.createElement("button");
//   removeFormButton.className = "btn btn-danger mb-3";
//   removeFormButton.innerText = "Delete an attribute";

//   item.append(removeFormButton);

//   removeFormButton.addEventListener("click", (e) => {
//     e.preventDefault();
//     item.remove();
//   });
// }
