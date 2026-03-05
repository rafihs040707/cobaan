function loadData(page = 1) {
    const status = document.getElementById("filterStatus")?.value || '';
    const bulan = document.getElementById("filterBulan")?.value || '';

    fetch(`filter_sertifikat.php?status=${status}&bulan=${bulan}&halaman=${page}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById("tableContainer").innerHTML = html;
        });
}

// 🔹 trigger saat dropdown berubah
document.getElementById("filterStatus").addEventListener("change", () => {
    loadData(1);
});
document.getElementById("filterBulan").addEventListener("change", () => {
    loadData(1);
});

// 🔹 handle pagination ajax
document.addEventListener("click", function (e) {
    if (e.target.classList.contains("page-ajax")) {
        e.preventDefault();
        const page = e.target.dataset.page;
        loadData(page);
    }
});

// =======================
// CHECK ALL → ROW
// =======================
document.getElementById('checkAll').addEventListener('change', function () {
    document.querySelectorAll('.row-check').forEach(cb => {
        cb.checked = this.checked;
    });
});

// =======================
// ROW → CHECK ALL
// =======================
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('row-check')) {

        const all = document.querySelectorAll('.row-check');
        const checked = document.querySelectorAll('.row-check:checked');
        const checkAll = document.getElementById('checkAll');

        // jika semua pending sudah dicentang
        checkAll.checked = all.length === checked.length;
    }
});
