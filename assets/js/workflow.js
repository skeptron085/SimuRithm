"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const pages = Array.from(document.querySelectorAll(".workflow-page"));
    const navigationItems = Array.from(document.querySelectorAll(".workflow-navigation [data-workflow-page]"));
    const sidebar = document.getElementById("simurithmSidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");

    function showPage(pageName, updateHistory) {
        const selectedPage = pages.find(function (page) {
            return page.id === pageName;
        });

        if (!selectedPage) {
            return;
        }

        pages.forEach(function (page) {
            page.hidden = page !== selectedPage;
        });

        navigationItems.forEach(function (item) {
            const isCurrent = item.dataset.workflowPage === pageName;
            item.classList.toggle("active", isCurrent);

            if (isCurrent) {
                item.setAttribute("aria-current", "page");
            } else {
                item.removeAttribute("aria-current");
            }
        });

        if (updateHistory && window.location.hash !== "#" + pageName) {
            window.history.pushState(null, "", "#" + pageName);
        }

        if (sidebar) {
            sidebar.classList.remove("show");
        }

        if (sidebarToggle) {
            sidebarToggle.setAttribute("aria-expanded", "false");
        }

        window.scrollTo({ top: 0, behavior: "smooth" });

        if (window.Chart && window.Chart.instances) {
            Object.values(window.Chart.instances).forEach(function (chart) {
                chart.resize();
            });
        }
    }

    window.showWorkflowPage = function (pageName) {
        showPage(pageName, true);
    };

    navigationItems.forEach(function (item) {
        item.addEventListener("click", function (event) {
            event.preventDefault();
            showPage(item.dataset.workflowPage, true);
        });
    });

    window.addEventListener("popstate", function () {
        showPage(window.location.hash.slice(1) || "upload", false);
    });

    window.addEventListener("hashchange", function () {
        showPage(window.location.hash.slice(1) || "upload", false);
    });

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            const isOpen = sidebar.classList.toggle("show");
            sidebarToggle.setAttribute("aria-expanded", String(isOpen));
        });
    }

    const initialPage = pages.some(function (page) {
        return page.id === window.location.hash.slice(1);
    }) ? window.location.hash.slice(1) : "upload";

    showPage(initialPage, false);

    if (window.location.hash !== "#" + initialPage) {
        window.history.replaceState(null, "", window.location.pathname + window.location.search + "#" + initialPage);
    }
});
