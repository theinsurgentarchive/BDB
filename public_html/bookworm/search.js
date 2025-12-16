// Triggered every time user types in the search box
function searchpartial(evt) {
  const text = document.getElementById("liveSearch").value;
  const results = document.getElementById("results");

  if (text.length < 1) {
    results.innerHTML = "";
    return;
  }

  const data = new FormData();
  data.append("booktitle", text);

  fetch("/~bdb/bookworm/searchAPI.php", {
    method: "POST",
    body: data,
  })
    .then((res) => res.json())
    .then((json) => displayResults(json))
    .catch((err) => console.error("Fetch error:", err));
}

function displayResults(json) {
  const results = document.getElementById("results");
  results.innerHTML = "";

  if (json.length === 0) {
    results.innerHTML = "<p>No books found.</p>";
    return;
  }

  json.forEach((row) => {
    let div = document.createElement("div");
    div.innerHTML = `
				<a class="result-item" href="/~bdb/bookworm/dynBook.php?bid=${row.book_id}">
					<strong>${row.title}</strong><br>
          <span>${row.genres}</span><br>
					<span>${row.author}</span>
				</a>
			`;
    results.append(div);
  });
}
