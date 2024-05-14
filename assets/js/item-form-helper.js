// let item_collection = document.querySelector("#item_itemCollection");

// const updateForm = async (data, url, method) => {
//   const req = await fetch(url, {
//     method: method,
//     body: data,
//     headers: {
//       "Content-Type": "application/x-www-form-urlencoded",
//       charset: "utf-8",
//     },
//   });

//   const text = await req.text();

//   return text;
// };

// const parseTextToHtml = (text) => {
//   const parser = new DOMParser();
//   const html = parser.parseFromString(text, "text/html");

//   return html;
// };

// item_collection.addEventListener("change", async (e) => {
//   let form = item_collection.closest("form");
//   let requestBody = e.target.getAttribute("name") + "=" + e.target.value;
//   let action = form.getAttribute("action");
//   let method = form.getAttribute("method");
//   console.log(requestBody);
//   console.log(action);
//   console.log(method);
//   let res = await updateForm(requestBody, action, method);
//   let html = parseTextToHtml(res);

//   let formFields = html.querySelectorAll(".form-control");

//   formFields = Array.from(formFields);
//   let customFields = formFields.filter((field) => {
//     if (
//       field["name"] === "item[name]" ||
//       field["name"] === "item[tags]" ||
//       field["name"] === "item[_token]"
//     ) {
//       return false;
//     }
//     return true;
//   });

//   let oldFormFields = form.querySelectorAll("div input");
//   oldFormFields = Array.from(oldFormFields);
//   let oldCustomFields = oldFormFields.filter((field) => {
//     if (
//       field["name"] === "item[name]" ||
//       field["name"] === "item[tags]" ||
//       field["name"] === "item[_token]"
//     ) {
//       return false;
//     }
//     return true;
//   });
//   // console.log(customFields);
//   for (let oldCustomField of oldCustomFields) {
//     for (let customField of customFields) {
//       if (oldCustomField.name !== customField.name) {
//         let oldCustomFieldWrapper = oldCustomField.closest("div");
//         oldCustomFieldWrapper.remove();

//         let customFieldWrapper = customField.closest("div");
//         form.appendChild(customFieldWrapper);
//       }
//     }
//   }
// });
