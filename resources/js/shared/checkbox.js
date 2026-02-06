// --- Make 'None' checkboxes exclusive within their group
function setupExclusiveNone(groupName, noneCheckboxId) {
    const none = document.getElementById(noneCheckboxId);
    if (!none) return;

    // find all checkboxes in the same named group (same name attr)
    const checkboxes = Array.from(
        document.querySelectorAll(
            'input[type="checkbox"][name="' + groupName + '[]"]'
        )
    );
 
    function onNoneChange() {
        if (none.checked) {
            checkboxes.forEach((cb) => {
                if (cb !== none) cb.checked = false;
            });
        }
    }

    function onOtherChange(e) {
        if (e.target !== none && e.target.checked) {
            none.checked = false;
        }
    }

    none.addEventListener("change", onNoneChange);
    checkboxes.forEach((cb) => cb.addEventListener("change", onOtherChange));
}

// Expose so other modules or inline scripts can call it
try {
    window.setupExclusiveNone = setupExclusiveNone;
} catch (e) {
    // ignore if window is not available in this environment
}

// Initialize exclusive behavior after DOM is ready
if (typeof document !== "undefined") {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            setupExclusiveNone("allergies", "allergy_none");
            setupExclusiveNone("treatments", "treat_none");
            setupExclusiveNone("covid", "covid_none");
        });
    } else {
        setupExclusiveNone("allergies", "allergy_none");
        setupExclusiveNone("treatments", "treat_none");
        setupExclusiveNone("covid", "covid_none");
    }
}

// Delegation-based fallback: listen for checkbox changes globally and enforce
// exclusivity for any group that uses a 'None' option. This helps when checkboxes
// are added dynamically or the setupExclusiveNone couldn't bind earlier.
if (typeof document !== "undefined") {
    document.addEventListener(
        "change",
        function (e) {
            const target = e.target;
            if (!target || target.type !== "checkbox") return;

            // Only handle checkboxes with name like 'group[]'
            const name = target.name || "";
            const match = name.match(/^(.*)\[\]$/);
            if (!match) return;
            const groupName = match[1];

            // find the 'None' checkbox in this group robustly:
            // prefer id ending with '_none', otherwise look for a checkbox whose
            // value equals 'None' (case-insensitive) or whose label/text indicates
            // not vaccinated (case-insensitive). This catches variants like
            // 'Not vaccinated' used in some templates.
            function findNoneCheckboxForGroup(group) {
                const selector =
                    'input[type="checkbox"][name="' + group + '[]"]';
                const all = Array.from(document.querySelectorAll(selector));
                if (!all.length) return null;

                // 1) id endsWith _none
                const byId = all.find(
                    (cb) => cb.id && cb.id.toLowerCase().endsWith("_none")
                );
                if (byId) return byId;

                // 2) value exactly 'none' (case-insensitive)
                const byValueNone = all.find(
                    (cb) =>
                        (cb.value || "").toString().trim().toLowerCase() ===
                        "none"
                );
                if (byValueNone) return byValueNone;

                // 3) value or label contains 'not vaccinated' (case-insensitive)
                const byNotVaccinated = all.find((cb) => {
                    const v = (cb.value || "").toString().trim().toLowerCase();
                    if (
                        v === "not vaccinated" ||
                        v.indexOf("not vaccinated") !== -1
                    )
                        return true;
                    // check label text if input is wrapped by label
                    const lab = cb.parentElement;
                    if (
                        lab &&
                        lab.textContent &&
                        lab.textContent
                            .toLowerCase()
                            .indexOf("not vaccinated") !== -1
                    )
                        return true;
                    return false;
                });
                if (byNotVaccinated) return byNotVaccinated;

                // 4) fallback: value includes 'none'
                return (
                    all.find(
                        (cb) =>
                            (cb.value || "")
                                .toString()
                                .toLowerCase()
                                .indexOf("none") !== -1
                    ) || null
                );
            }

            const none = findNoneCheckboxForGroup(groupName);

            // If 'None' is the one changed and was checked, clear others
            if (none && target === none && none.checked) {
                const others = document.querySelectorAll(
                    'input[type="checkbox"][name="' + groupName + '[]"]'
                );
                others.forEach((cb) => {
                    if (cb !== none) cb.checked = false;
                });
                return;
            }

            // If another checkbox was checked, uncheck the 'None' box
            if (none && target !== none && target.checked) {
                none.checked = false;
            }
        },
        true
    );
}
