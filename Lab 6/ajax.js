function fetchBooks() {
    fetch('fetch.php')
        .then(response => response.json())
        .then(data => {
            let tableBody = document.querySelector("#bookTable tbody");
            tableBody.innerHTML = "";

            data.forEach(book => {
                let row = `<tr>
                    <td>${book.id}</td>
                    <td contenteditable="true" data-id="${book.id}" data-column="title">${book.title}</td>
                    <td contenteditable="true" data-id="${book.id}" data-column="author">${book.author}</td>
                    <td contenteditable="true" data-id="${book.id}" data-column="publish_date">${book.year}</td>
                </tr>`;
                tableBody.innerHTML += row;
            });

            addEditListeners();
        });
}

function addEditListeners() {
    document.querySelectorAll("td[contenteditable='true']").forEach(cell => {
        cell.addEventListener("blur", function() {
            let id = this.getAttribute("data-id");
            let column = this.getAttribute("data-column");
            let value = this.innerText;

            updateBook(id, column, value);
        });
    });
}

function updateBook(id, column, value) {
    let formData = new FormData();
    formData.append("id", id);
    formData.append("column", column);
    formData.append("value", value);

    fetch("update.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log(data);
    });
}

function showBookById(){
    fetch('fetch.php')
        .then(response => response.json())
        .then(data => {
            let tableBody = document.querySelector("#bookIdTable tbody");
            tableBody.innerHTML = "";

            data.forEach(book => {
                let row = `<tr>
                    <td id="ID" data-id="${book.id}">${book.id}</td>
                </tr>`;
                tableBody.innerHTML += row;
            });

            addIdListeners();
        });
}

function fetchBookById(ID) {
    let id = ID;

    fetch(`fetch.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (Object.keys(data).length > 0) {
                document.getElementById("bookDetails").innerHTML = `
                    <p>🆔 ID: ${data.id}</p>
                    <p>📖 Title: ${data.title}</p>
                    <p>✍️ Author: ${data.author}</p>
                    <p>📅 Publish Date: ${data.year}</p>
                `;
            } else {
                document.getElementById("bookDetails").innerHTML = "❌ No book found!";
            }
        });
}

function addIdListeners() {
    document.querySelectorAll("[id='ID']").forEach(cell => {
        cell.addEventListener("click", function() {
            let id = this.getAttribute("data-id");

            fetchBookById(id);
        });
    });
}