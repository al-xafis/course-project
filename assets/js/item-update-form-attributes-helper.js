document.addEventListener("DOMContentLoaded", () => {
  let item_collection = document.querySelector("#item_update_itemCollection");

  const updateForm = async (data, url, method) => {
    const req = await fetch(url, {
      method: method,
      body: data,
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
        charset: "utf-8",
      },
    });

    const text = await req.json();

    return text;
  };

  item_collection.addEventListener("change", async (e) => {
    let form = item_collection.closest("form");
    let requestBody = "collectionId" + "=" + e.target.value;
    let method = form.getAttribute("method");

    let customAttributes = await updateForm(
      requestBody,
      "/collection/get",
      method
    );

    let oldCustomAttributes = form.querySelectorAll(".dynamic-field");
    for (let attribute of oldCustomAttributes) {
      attribute.closest("div").remove();
    }

    for (let attribute of customAttributes) {
      let newInputWrapper = document.createElement("div");
      newInputWrapper.classList.add("mb-3");

      let newLabel = document.createElement("label");
      newLabel.classList.add("form-label");
      let name = attribute["name"].split("_").join(" ").toLowerCase();
      name = name.charAt(0).toUpperCase() + name.slice(1);
      newLabel.innerText = name;
      newInputWrapper.appendChild(newLabel);

      let newInput = document.createElement("input");
      newInput.id = "item_update_" + attribute["name"].toLowerCase();
      newInput.name = "item_update[" + attribute["name"].toLowerCase() + "]";
      newInput.classList.add("dynamic-field", "form-control");
      newInputWrapper.appendChild(newInput);
      form.appendChild(newInputWrapper);
      console.log(newInput);
    }
  });
});
