    (function() {
        // ===============================
        // CONFIG / ELEMENTS (shared)
        // ===============================
        const MODAL_ID = 'editConsultationModal';
        const FORM_ID = 'editConsultationFormModal';

        // Outcome-related elements (kept here so both the modal controller and outcome logic share the same refs)
        const outcomeSelect = document.getElementById('edit_outcome_select');
        const outcomeInput = document.getElementById('sent-home-input');
        const outcomeGroup = document.getElementById('sentHomeGroup');
        const outcomeHidden = document.getElementById('final_outcome_input');

        // ===============================
        // OUTCOME LOGIC (toggling + hidden value)
        // ===============================
        // Encapsulated so EditConsultationModal and other code can reuse it
        const OutcomeApi = (function() {
            const sel = outcomeSelect;
            const input = outcomeInput;
            const group = outcomeGroup;
            const hidden = outcomeHidden;

            const isSentHome = () => sel && sel.value === 'sent home with:';

            function toggle(show, focusInput = false) {
                if (!group) return;
                group.classList.toggle('d-none', !show);
                if (input) {
                    input.required = !!show;
                    if (show && focusInput) {
                        // focus after select UI closes (safe)
                        setTimeout(() => input.focus(), 0);
                    }
                }
            }

            function updateHidden() {
                if (!hidden || !sel) return;
                if (isSentHome()) {
                    const name = (input?.value || '').trim();
                    hidden.value = name ? 'sent home with: ' + name : 'sent home with:';
                } else {
                    hidden.value = sel.value || '';
                }
            }

            // Wire DOM events (safe — only if elements exist)
            if (sel) {
                sel.addEventListener('change', () => {
                    const show = isSentHome();
                    toggle(show, show);
                    updateHidden();
                });
                // no 'click' listener — avoids stealing focus from select
            }
            if (input) {
                input.addEventListener('input', updateHidden);
            }

            // initialize (do not autofocus)
            const initShow = input && input.dataset && input.dataset.initShow === '1';
            toggle(initShow, false);
            updateHidden();

            // When modal opens (Bootstrap event), recompute visibility (useful when content is injected)
            const modalEl = document.getElementById(MODAL_ID);
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', () => {
                    toggle(isSentHome(), false);
                    updateHidden();
                });
            }

            // Expose a minimal API for other scripts to use (like modal population)
            const api = {
                selectEl: sel,
                inputEl: input,
                hiddenEl: hidden,
                toggle,
                updateHidden,
                isSentHome
            };

            // also make this available globally like before (keeps compatibility)
            window.__editOutcomeModal = api;

            return api;
        })();


        // ===============================
        // BOOTSTRAP MODAL HELPER
        // ===============================
        function getBsModal() {
            const modalEl = document.getElementById(MODAL_ID);
            if (!modalEl || !window.bootstrap || !bootstrap.Modal) return null;
            return bootstrap.Modal.getOrCreateInstance(modalEl);
        }

        // safe setter for form inputs by name
        function safeSetByName(name, value) {
            const form = document.getElementById(FORM_ID);
            if (!form) return;
            const el = form.querySelector(`[name="${name}"]`);
            if (!el) return;
            el.value = value ?? '';
        }

        // populate the common consultation fields
        function populateBasicFields(dataset = {}) {
            safeSetByName('chief_complaint', dataset.chiefComplaint);
            safeSetByName('temperature', dataset.temperature);
            safeSetByName('blood_pressure', dataset.bloodPressure);
            safeSetByName('pulse_rate', dataset.pulseRate);
            safeSetByName('respiratory_rate', dataset.respiratoryRate);
            safeSetByName('spo2', dataset.spo2);
            safeSetByName('lmp', dataset.lmp);
            safeSetByName('pain_scale', dataset.painScale);
            safeSetByName('assessment', dataset.assessment);
            safeSetByName('intervention', dataset.intervention);
        }

        // fallback outcome population if OutcomeApi isn't present (kept for safety)
        function outcomeFallbackPopulate(outcome) {
            const api = OutcomeApi;
            if (!api.selectEl || !api.hiddenEl) return;
            if (typeof outcome === 'string' && outcome.toLowerCase().startsWith('sent home with:')) {
                api.selectEl.value = 'sent home with:';
                if (api.inputEl) {
                    api.inputEl.value = outcome.replace(/^sent home with:\s*/i, '');
                    api.toggle(true, false);
                }
            } else {
                api.selectEl.value = outcome || '';
                if (api.inputEl) api.inputEl.value = '';
                api.toggle(false, false);
            }
            api.updateHidden();
            try {
                api.selectEl.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            } catch (e) {}
        }


        // ===============================
        // EDIT CONSULTATION MODAL CONTROLLER
        // (open(fromEl) populates form & shows modal)
        // ===============================
        window.EditConsultationModal = (function() {
            function open(fromEl = null) {
                const modalEl = document.getElementById(MODAL_ID);
                const form = document.getElementById(FORM_ID);
                if (!modalEl || !form) return;

                const dataset = (fromEl && fromEl.dataset) ? fromEl.dataset : {};

                // set form action (data-update-url OR data-updateUrl)
                const updateUrl = dataset.updateUrl || fromEl?.getAttribute('data-update-url') || '#';
                form.action = updateUrl;

                // basic fields
                populateBasicFields(dataset);

                // outcome: prefer OutcomeApi (shared), otherwise fallback
                const outcome = (dataset.outcome !== undefined && dataset.outcome !== null) ? dataset.outcome : '';
                const api = window.__editOutcomeModal || OutcomeApi;
                if (api && api.selectEl) {
                    if (typeof outcome === 'string' && outcome.toLowerCase().startsWith('sent home with:')) {
                        api.selectEl.value = 'sent home with:';
                        if (api.inputEl) api.inputEl.value = outcome.replace(/^sent home with:\s*/i, '');
                        api.toggle(true, false); // do not autofocus to avoid stealing select UI
                    } else {
                        api.selectEl.value = outcome || '';
                        if (api.inputEl) api.inputEl.value = '';
                        api.toggle(false, false);
                    }
                    if (api.updateHidden) api.updateHidden();
                    try {
                        api.selectEl.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    } catch (e) {}
                } else {
                    outcomeFallbackPopulate(outcome);
                }

                // show modal
                const bs = getBsModal();
                if (bs) bs.show();
            }

            function close() {
                const bs = getBsModal();
                if (bs) bs.hide();
            }

            return {
                open,
                close
            };
        })();


        // ===============================
        // CLICK DELEGATION: open Edit modal
        // - Any element with data-update-url will populate & open the Edit modal
        // - Keeps behaviour consistent for "edit" links/buttons in view pane
        // ===============================
        document.addEventListener('click', function(e) {
            const el = e.target.closest('[data-update-url]');
            if (!el) return;
            e.preventDefault();
            window.EditConsultationModal.open(el);
        });

        // ===============================
        // VIEW CONSULTATION NAVIGATOR (robust)
        // - Handles next/previous when viewing multiple records inside the View modal
        // - Replaces previous ConsultationModal implementation
        // ===============================
        const ConsultationModal = (function() {
            const CONTAINER_ID = 'recordContainer';

            // helper: return an array of pane elements (keeps backwards compatibility)
            function getPanes() {
                const container = document.getElementById(CONTAINER_ID);
                if (!container) return [];
                // Prefer explicit panes marked with data-record, otherwise fall back to direct children
                let panes = Array.from(container.querySelectorAll('[data-record]'));
                if (!panes.length) {
                    panes = Array.from(container.children).filter(c => c.nodeType === 1);
                }
                return panes;
            }

            // return index of the currently visible pane
            function currentIndex() {
                const panes = getPanes();
                if (!panes.length) return -1;
                return panes.findIndex(p => !p.hasAttribute('hidden'));
            }

            // clamp index
            function clamp(index, len) {
                return Math.max(0, Math.min(index, Math.max(0, len - 1)));
            }

            // update a single pane DOM: show if i === index, hide otherwise
            function setPaneVisible(panes, index) {
                panes.forEach((p, i) => {
                    const show = i === index;
                    if (show) {
                        p.removeAttribute('hidden');
                        p.setAttribute('aria-hidden', 'false');
                    } else {
                        p.setAttribute('hidden', '');
                        p.setAttribute('aria-hidden', 'true');
                    }
                });
            }

            // update counter display and prev/next button states for the active pane
            function updateControlsForPane(panes, index) {
                const pane = panes[index];
                if (!pane) return;

                // update any .recordCounter element inside pane, and wrapper
                const counter = pane.querySelector('.recordCounter');
                if (counter) counter.textContent = String(index + 1);

                const wrapper = pane.querySelector('.recordCounterWrapper');
                if (wrapper) {
                    wrapper.innerHTML = `
                <span class="recordCounter">${index + 1}</span>
                <span class="text-muted"> / ${panes.length}</span>
            `;
                }

                // per-pane prev/next buttons
                const prevBtn = pane.querySelector('.js-prev');
                const nextBtn = pane.querySelector('.js-next');
                if (prevBtn) prevBtn.disabled = (index === 0);
                if (nextBtn) nextBtn.disabled = (index === panes.length - 1);

                // also update any globally-scoped prev/next controls (outside panes) with attributes data-cm-action
                const globalPrev = document.querySelector('[data-cm-action="prev"]');
                const globalNext = document.querySelector('[data-cm-action="next"]');
                if (globalPrev) globalPrev.disabled = (index === 0);
                if (globalNext) globalNext.disabled = (index === panes.length - 1);
            }

            // core: show pane at index
            function show(index) {
                const panes = getPanes();
                if (!panes.length) return;
                const idx = clamp(index, panes.length);
                setPaneVisible(panes, idx);
                updateControlsForPane(panes, idx);
            }

            function next() {
                const idx = currentIndex();
                if (idx < 0) return;
                show(idx + 1);
            }

            function prev() {
                const idx = currentIndex();
                if (idx < 0) return;
                show(idx - 1);
            }

            // Initialization routine (safe to call multiple times)
            function init() {
                const panes = getPanes();
                if (!panes.length) return;
                // If none visible, show first
                if (panes.every(p => p.hasAttribute('hidden'))) {
                    show(0);
                    return;
                }
                // If a pane is visible, ensure controls reflect that state
                const idx = currentIndex();
                if (idx >= 0) show(idx);
            }

            // delegated click handlers for per-pane buttons (works even if buttons are added later)
            document.addEventListener('click', function(e) {
                const prevEl = e.target.closest('.js-prev');
                if (prevEl) {
                    e.preventDefault();
                    prev();
                    return;
                }
                const nextEl = e.target.closest('.js-next');
                if (nextEl) {
                    e.preventDefault();
                    next();
                    return;
                }

                // support global controls (outside panes) that use data-cm-action="prev" / "next"
                const global = e.target.closest('[data-cm-action]');
                if (global) {
                    const action = global.getAttribute('data-cm-action');
                    if (action === 'prev') {
                        e.preventDefault();
                        prev();
                    } else if (action === 'next') {
                        e.preventDefault();
                        next();
                    }
                }
            });

            // init now (in case script is executed after DOM ready)
            try {
                init();
            } catch (err) {
                // swallow to avoid breaking other script; useful for debugging in console
                // console.warn('ConsultationModal init error', err);
            }

            // also initialize on DOMContentLoaded in case the modal HTML is injected later
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                // already ready: run again to be safe
                setTimeout(init, 0);
            }

            return {
                show,
                next,
                prev,
                init
            };
        })();

    })();
