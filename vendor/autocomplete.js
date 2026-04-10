document.addEventListener("DOMContentLoaded", function () {

    let form = document.querySelector("form");
    let layoutSelect = document.getElementById("file_layout");
    let materiSection = document.getElementById("materi-section");
    let wrapper = document.getElementById("materi-wrapper");

    // SIMPAN TEMPLATE DEFAULT (1 ITEM AWAL)
    let defaultItem = wrapper.innerHTML;

    // =========================
    // RESET FORM
    // =========================
    form.addEventListener("reset", function () {

        setTimeout(() => {

            // reset dropdown template
            layoutSelect.selectedIndex = 0;

            // sembunyikan section materi
            materiSection.style.display = "none";

            // kembalikan hanya 1 item
            wrapper.innerHTML = defaultItem;

            // kosongkan semua input & select
            wrapper.querySelectorAll("input, select").forEach(el => {
                if (el.tagName === "SELECT") {
                    el.selectedIndex = 0;
                } else {
                    el.value = "";
                }
            });

        }, 10); // delay supaya reset bawaan HTML selesai dulu
    });

    // =========================
    // CEK TEMPLATE (TAMPILKAN MATERI)
    // =========================
    function cekLayout() {
        let selectedText = layoutSelect.options[layoutSelect.selectedIndex].text;

        if (selectedText.toLowerCase().includes("fb")) {
            materiSection.style.display = "block";
        } else {
            materiSection.style.display = "none";
        }
    }

    // saat load
    cekLayout();

    // saat ganti template
    layoutSelect.addEventListener("change", cekLayout);

    // =========================
    // EVENT TAMBAH & HAPUS
    // =========================
    document.addEventListener("click", function (e) {

        // TAMBAH
        if (e.target && e.target.id === "tambah") {

            let html = `
<div class="row materi-item mb-3">

    <div class="col-md-4 mt-2">
    <input type="text" name="materi[]" class="form-control materi-input" placeholder="Materi" autocomplete="off">
    </div>
    <div class="col-md-4 mt-2">
        <input type="text" name="durasi[]" class="form-control" placeholder="Masukan durasi/jam atau nilai atau skor" autocomplete="off">
    </div>
    <div class="col-md-2 mt-2">
        <button type="button" class="btn btn-danger hapus">Hapus</button>
    </div>

</div>
`;

            wrapper.insertAdjacentHTML("beforeend", html);
        }

        // HAPUS
        if (e.target && e.target.classList.contains("hapus")) {

            let items = document.querySelectorAll(".materi-item");

            // minimal harus tersisa 1
            if (items.length > 1) {
                e.target.closest(".materi-item").remove();
            } else {
                alert("Minimal harus ada 1 materi!");
            }
        }

    });

    // =========================
    // AUTOCOMPLETE MATERI
    // =========================
    document.addEventListener("input", function (e) {

        if (e.target.classList.contains("materi-input")) {

            let input = e.target;
            let keyword = input.value;

            // hapus dropdown lama
            let oldList = input.parentNode.querySelector(".autocomplete-list");
            if (oldList) oldList.remove();

            if (keyword.length < 2) return;

            fetch(BASE_URL + "admin/sertifikat/get_materi.php?q=" + keyword)
    .then(res => {
        return res.json();
    })
    .then(data => {


        let list = document.createElement("div");
        list.classList.add("autocomplete-list");

        list.style.position = "absolute";
        list.style.background = "#fff";
        list.style.border = "1px solid #ccc";
        list.style.width = input.offsetWidth + "px";
        list.style.zIndex = "999";

        data.forEach(item => {
            let div = document.createElement("div");
            div.textContent = item;
            div.style.padding = "5px";
            div.style.cursor = "pointer";

            div.addEventListener("click", function () {
                e.stopPropagation();
                input.value = item;
                list.remove();
            });

            list.appendChild(div);
        });

        input.parentNode.style.position = "relative";
        input.parentNode.appendChild(list);
    })
    .catch(err => {
        console.log("ERROR FETCH:", err); // 🔥 kalau gagal
    });
        }
    });
document.addEventListener("focusin", function (e) {

    if (e.target.classList.contains("materi-input")) {

        let input = e.target;
        let keyword = input.value;

        if (keyword.length < 2) return;

        // hapus dropdown lama
        let oldList = input.parentNode.querySelector(".autocomplete-list");
        if (oldList) oldList.remove();

        fetch(BASE_URL + "admin/sertifikat/get_materi.php?q=" + keyword)
            .then(res => res.json())
            .then(data => {

                let list = document.createElement("div");
                list.classList.add("autocomplete-list");

                list.style.position = "absolute";
                list.style.background = "#fff";
                list.style.border = "1px solid #ccc";
                list.style.width = input.offsetWidth + "px";
                list.style.zIndex = "999";

                data.forEach(item => {
                    let div = document.createElement("div");
                    div.textContent = item;
                    div.style.padding = "5px";
                    div.style.cursor = "pointer";

                    div.addEventListener("click", function () {
                        input.value = item;
                        list.remove();
                    });

                    list.appendChild(div);
                });

                input.parentNode.style.position = "relative";
                input.parentNode.appendChild(list);
            });
    }
});

    // klik luar -> hapus dropdown
    document.addEventListener("click", function (e) {
    document.querySelectorAll(".autocomplete-list").forEach(el => {

        let input = el.parentNode.querySelector(".materi-input");

        if (!el.contains(e.target) && e.target !== input) {
            el.remove();
        }

    });
});
});