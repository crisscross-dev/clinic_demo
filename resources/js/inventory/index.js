document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchItem");
    const categorySelect =
        document.getElementById("categoryFilter") ||
        document.getElementById("departmentFilter");
    const stockSelect =
        document.getElementById("stockFilter") ||
        document.getElementById("stocksFilter");

    function getComputedStock(tr) {
        // Prefer data attribute; fallback to computing from Total Stock cell (index 6)
        const ds = (tr.dataset.stock || "").toLowerCase();
        if (ds) return ds;
        const cells = tr.children || [];
        const stockText = cells.length > 6 ? cells[6].textContent || "0" : "0";
        const n = parseInt(stockText.replace(/[^\d-]/g, ""), 10);
        if (!isFinite(n) || isNaN(n)) return "";
        return n <= 0 ? "out" : n < 5 ? "low" : "in";
    }

    function applyFilters() {
        const q = ((searchInput && searchInput.value) || "")
            .toLowerCase()
            .trim();
        const cat = ((categorySelect && categorySelect.value) || "")
            .toLowerCase()
            .trim();
        const stock = ((stockSelect && stockSelect.value) || "")
            .toLowerCase()
            .trim();

        document.querySelectorAll("#inventoryTable tbody tr").forEach((tr) => {
            const textMatch = tr.textContent.toLowerCase().includes(q);
            const rowCat = (
                tr.dataset.category ||
                (tr.children[2] && tr.children[2].textContent
                    ? tr.children[2].textContent
                    : "")
            )
                .toLowerCase()
                .trim();
            const rowStock = getComputedStock(tr);
            const catMatch = !cat || rowCat === cat;
            const stockMatch = !stock || rowStock === stock;
            tr.style.display =
                textMatch && catMatch && stockMatch ? "" : "none";
        });
    }

    if (searchInput) searchInput.addEventListener("input", applyFilters);
    if (categorySelect) categorySelect.addEventListener("change", applyFilters);
    if (stockSelect) stockSelect.addEventListener("change", applyFilters);

    // Initial filter (no filters applied, ensures consistent state)
    applyFilters();
});
