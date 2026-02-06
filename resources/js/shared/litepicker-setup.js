function formatDateYMD(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, "0");
    const d = String(date.getDate()).padStart(2, "0");
    return `${y}-${m}-${d}`;
}

export function setupRangePicker({
    button,
    startInput,
    endInput,
    labelElement,
    onApply,
    days = 7,
    startDate,
    endDate,
    openAtToday = false,
    preset = true,
}) {
    if (!button) return null;

    const ensureLitepicker = async () => {
        if (window.Litepicker) return window.Litepicker;
        try {
            const mod = await import("litepicker");
            return mod.default || mod;
        } catch {
            return new Promise((resolve) => {
                const iv = setInterval(() => {
                    if (window.Litepicker) {
                        clearInterval(iv);
                        resolve(window.Litepicker);
                    }
                }, 100);
            });
        }
    };

    (async () => {
        const Litepicker = await ensureLitepicker();
        if (!Litepicker) return null;

        // Prepare default date range (only if preset is true)
        let defaultStart, defaultEnd;
        if (preset) {
            if (startDate && endDate) {
                defaultStart = new Date(startDate);
                defaultEnd = new Date(endDate);
            } else {
                // Calculate default range from days parameter
                const today = new Date();
                defaultEnd = today;
                defaultStart = new Date();
                defaultStart.setDate(today.getDate() - (days - 1));
            }
        }

        const baseOptions = {
            element: button,
            singleMode: false,
            autoApply: true,
            format: "MMM D, YYYY",
            setup: (p) => {
                // guard to avoid duplicate/partial selection events
                let lastApplied = null;
                p.on("selected", (start, end) => {
                    // sometimes selected fires with only start; wait for both
                    if (
                        !start ||
                        !end ||
                        !start.dateInstance ||
                        !end.dateInstance
                    )
                        return;

                    const s = formatDateYMD(start.dateInstance);
                    const e = formatDateYMD(end.dateInstance);

                    const key = `${s}_${e}`;
                    if (lastApplied === key) return; // already applied this range
                    lastApplied = key;

                    if (startInput) startInput.value = s;
                    if (endInput) endInput.value = e;

                    const label =
                        start.format("MMM D, YYYY") +
                        " – " +
                        end.format("MMM D, YYYY");
                    if (labelElement) labelElement.textContent = label;
                    else button.textContent = label;

                    onApply?.(s, e);
                });

                // When opening, try to ensure today's month is visible
                if (openAtToday) {
                    p.on("show", () => {
                        try {
                            const today = new Date();
                            // If the API exposes gotoDate (Litepicker v2), use it
                            if (typeof p.gotoDate === "function") {
                                p.gotoDate(today);
                            }
                            // Otherwise, do nothing to avoid changing selection
                        } catch {}
                    });
                }
            },
        };

        // Only pass start/end if preset is enabled
        const options = preset
            ? { ...baseOptions, startDate: defaultStart, endDate: defaultEnd }
            : baseOptions;

        const picker = new Litepicker(options);

        // Set initial display/inputs only when presetting
        if (preset) {
            const formatDisplayDate = (date) => {
                return date.toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                    year: "numeric",
                });
            };

            const initialLabel = `${formatDisplayDate(
                defaultStart
            )} – ${formatDisplayDate(defaultEnd)}`;
            if (labelElement) labelElement.textContent = initialLabel;
            else button.textContent = initialLabel;

            // Set initial input values
            if (startInput) startInput.value = formatDateYMD(defaultStart);
            if (endInput) endInput.value = formatDateYMD(defaultEnd);
        }

        return picker;
    })();

    return null;
}
