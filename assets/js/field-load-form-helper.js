// document.addEventListener("DOMContentLoaded", () => {
//   let item_collection = document.querySelector("#item_itemCollection");

//   const updateForm = async (data, url, method) => {
//     const req = await fetch(url, {
//       method: method,
//       body: data,
//       headers: {
//         "Content-Type": "application/x-www-form-urlencoded",
//         charset: "utf-8",
//       },
//     });

//     const text = await req.json();

//     return text;
//   };

//   const parseTextToHtml = (text) => {
//     const parser = new DOMParser();
//     const html = parser.parseFromString(text, "text/html");

//     return html;
//   };

//   item_collection.addEventListener("change", async (e) => {
//     let form = item_collection.closest("form");
//     let requestBody = "collectionId" + "=" + e.target.value;
//     let action = form.getAttribute("action");
//     let method = form.getAttribute("method");
//     // console.log(requestBody);
//     // console.log(action);
//     // console.log(method);
//     let customAttributes = await updateForm(
//       requestBody,
//       "/collection/get",
//       method
//     );
//     console.log(customAttributes);
//     // let html = parseTextToHtml(res);

//     let nameField = form.querySelector("#item_name");

//     let oldCustomAttributes = form.querySelectorAll(".dynamic-field");
//     for (let attribute of oldCustomAttributes) {
//       attribute.closest("div").remove();
//     }

//     for (let attribute of customAttributes) {
//       let newInputWrapper = document.createElement("div");
//       newInputWrapper.classList.add("mb-3");

//       let newLabel = document.createElement("label");
//       newLabel.classList.add("form-label");
//       newLabel.innerText = attribute["name"];
//       newInputWrapper.appendChild(newLabel);

//       let newInput = document.createElement("input");
//       newInput.id = "item_" + attribute["name"].toLowerCase();
//       newInput.name = "item[" + attribute["name"].toLowerCase() + "]";
//       newInput.classList.add("dynamic-field", "form-control");
//       newInputWrapper.appendChild(newInput);
//       form.appendChild(newInputWrapper);
//     }
//   });
// });
