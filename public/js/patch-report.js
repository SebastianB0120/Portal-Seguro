(() => {
    const status = document.getElementById('capture-status');
    const gallery = document.getElementById('capture-gallery');
    const emptyMessage = document.getElementById('capture-empty');
    const captureButton = document.getElementById('capture-report');
    const printButton = document.getElementById('print-report');
    const zapSection = document.getElementById('report-zap');
    const zapButton = document.getElementById('run-zap-scan');
    const zapStatusMessage = document.getElementById('zap-status-message');
    const zapResultMessage = document.getElementById('zap-result-message');
    const zapResults = document.getElementById('zap-results');
    const zapStatusBadge = zapSection?.querySelector('.report-status-badge');
    const zapSummaryStatus = document.querySelector('#report-overview .report-facts div:last-child dd');
    const targets = Array.from(document.querySelectorAll('[data-report-capture]'));

    if (!status || !gallery || targets.length === 0) {
        return;
    }

    const captureReport = async () => {
        if (typeof window.html2canvas !== 'function') {
            status.textContent = 'No se pudo cargar el módulo de capturas. Comprueba la conexión a Internet y vuelve a intentarlo.';
            status.classList.add('is-error');
            return;
        }

        status.textContent = 'Generando capturas PNG...';
        status.classList.remove('is-error');
        captureButton.disabled = true;
        gallery.replaceChildren();

        try {
            if (document.fonts && document.fonts.ready) {
                await document.fonts.ready;
            }

            for (const target of targets) {
                const canvas = await window.html2canvas(target, {
                    backgroundColor: '#ffffff',
                    logging: false,
                    scale: Math.min(window.devicePixelRatio || 1, 2),
                    useCORS: true,
                });
                const imageUrl = canvas.toDataURL('image/png');
                const filename = `informe-parches-${target.dataset.reportCapture.toLowerCase()}.png`;
                const item = document.createElement('article');
                const image = document.createElement('img');
                const link = document.createElement('a');

                item.className = 'capture-item';
                image.src = imageUrl;
                image.alt = `Captura del apartado ${target.dataset.reportCapture}`;
                image.loading = 'lazy';
                link.href = imageUrl;
                link.download = filename;
                link.textContent = `Descargar ${target.dataset.reportCapture}.png`;
                item.append(image, link);
                gallery.append(item);
            }

            if (emptyMessage) {
                emptyMessage.hidden = true;
            }
            status.textContent = `Capturas listas: ${targets.length} imágenes PNG. Usa los enlaces para descargarlas.`;
        } catch (error) {
            status.textContent = 'No se pudieron generar las capturas. Vuelve a intentarlo o usa Imprimir / Guardar PDF.';
            status.classList.add('is-error');
        } finally {
            captureButton.disabled = false;
        }
    };

    const runZapScan = async () => {
        if (!zapSection || !zapButton || !zapStatusMessage || !zapResults) {
            return;
        }

        zapButton.disabled = true;
        zapButton.textContent = 'Analizando con ZAP...';
        zapStatusMessage.textContent = 'Conectando con ZAP; el análisis pasivo puede tardar hasta un minuto...';

        try {
            const response = await fetch(zapSection.dataset.zapScanUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': zapSection.dataset.csrfToken,
                },
                credentials: 'same-origin',
            });
            const result = await response.json();

            if (zapStatusBadge) {
                zapStatusBadge.textContent = result.status || 'Estado no disponible';
            }
            if (zapSummaryStatus) {
                zapSummaryStatus.textContent = result.status || 'Estado no disponible';
            }
            if (zapResultMessage) {
                zapResultMessage.textContent = result.message || 'ZAP no devolvió un mensaje de estado.';
            }

            zapResults.replaceChildren();
            if (result.zapVersion) {
                const scanInfo = document.createElement('p');
                scanInfo.textContent = `Versión ZAP: ${result.zapVersion}. Fecha: ${result.scannedAt || 'No disponible'}. Objetivo: ${result.targetUrl || ''}.`;
                zapResults.append(scanInfo);
            }

            const counts = document.createElement('dl');
            counts.className = 'report-facts';
            counts.id = 'zap-counts';
            Object.entries(result.counts || {}).forEach(([risk, count]) => {
                const item = document.createElement('div');
                const label = document.createElement('dt');
                const value = document.createElement('dd');
                label.textContent = risk;
                value.textContent = String(count);
                item.append(label, value);
                counts.append(item);
            });
            zapResults.append(counts);

            const alerts = document.createElement('ol');
            alerts.className = 'report-zap-alerts';
            alerts.id = 'zap-alert-list';
            (result.alerts || []).forEach((alert) => {
                const item = document.createElement('li');
                const title = document.createElement('strong');
                const url = document.createElement('code');
                title.textContent = `${alert.risk || 'Informational'}: ${alert.alert || 'Alerta sin título'}`;
                url.textContent = alert.url || '';
                item.append(title, document.createTextNode(' '), url);
                alerts.append(item);
            });
            zapResults.append(alerts);

            zapStatusMessage.textContent = result.message || result.status || 'Análisis finalizado.';
            if (!response.ok) {
                zapStatusMessage.classList.add('is-error');
            } else {
                zapStatusMessage.classList.remove('is-error');
            }
            await captureReport();
        } catch (error) {
            zapStatusMessage.textContent = 'No se pudo completar la solicitud a ZAP. Revisa que el servicio local esté iniciado.';
            zapStatusMessage.classList.add('is-error');
        } finally {
            zapButton.disabled = false;
            zapButton.textContent = 'Ejecutar análisis pasivo';
        }
    };

    captureButton.addEventListener('click', captureReport);
    zapButton?.addEventListener('click', runZapScan);
    printButton.addEventListener('click', () => window.print());
    window.addEventListener('load', () => {
        window.setTimeout(captureReport, 250);
        if (zapSection) {
            window.setTimeout(runZapScan, 500);
        }
    }, { once: true });
})();
