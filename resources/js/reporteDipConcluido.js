import Chart from 'chart.js/auto';

(function () {
  function parseSeriesFromCanvas(id) {
    const el = document.getElementById(id);
    if (!el) return [];
    const raw = el.dataset.series || '[]';
    try { return JSON.parse(raw); } catch { return []; }
  }

  function makeBarChart(canvasId, { labels, datasets, stacked = false, title = "" }) {
    const el = document.getElementById(canvasId);
    if (!el || typeof Chart === "undefined") return null;

    const ctx = el.getContext("2d");
    return new Chart(ctx, {
      type: "bar",
      data: { labels, datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { stacked },
          y: { stacked, beginAtZero: true }
        },
        plugins: {
          title: { display: !!title, text: title }
        }
      }
    });
  }

  function initEgresadosChart() {
    const data = parseSeriesFromCanvas("egresadosAnualChart");
    if (!Array.isArray(data) || data.length === 0) return null;

    const labels = data.map(d => `${d.nombre} (${d.grupo})`);
    const values = data.map(d => Number(d.egresados || 0));

    return makeBarChart("egresadosAnualChart", {
      labels,
      datasets: [{
        label: "Número de Egresados",
        data: values,
        backgroundColor: "rgb(17, 37, 67)",
        borderWidth: 1
      }]
    });
  }

  function initComparacionChart() {
    const data = parseSeriesFromCanvas("comparacionEstatusChart");
    if (!Array.isArray(data) || data.length === 0) return null;

    const labels   = data.map(d => `${d.nombre} (${d.grupo})`);
    const activos  = data.map(d => Number(d.activos   || 0));
    const egresados= data.map(d => Number(d.egresados || 0));

    return makeBarChart("comparacionEstatusChart", {
      labels,
      datasets: [
        { label: "Activos",   data: activos,   backgroundColor: "rgb(17, 37, 67)" },
        { label: "Egresados", data: egresados, backgroundColor: "rgb(36, 86, 174)" }
      ],
      stacked: true
    });
  }

  function initTabs() {
    const tabs = document.querySelectorAll(".tab");
    const sections = document.querySelectorAll("section");
    if (!tabs.length || !sections.length) return;

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");

        sections.forEach(s => (s.style.display = "none"));
        const target = document.getElementById(`tab-${tab.dataset.tab}`);
        if (target) target.style.display = "block";
      });
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    initEgresadosChart();
    initComparacionChart();
    initTabs();
  });
})();