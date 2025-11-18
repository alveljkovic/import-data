function openModal(id) {
    const modalEl = document.getElementById(id);
    if (!modalEl) return;

    if (typeof bootstrap !== "undefined") {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        return;
    }

    modalEl.classList.add('show');
    modalEl.style.display = 'block';
    modalEl.removeAttribute('aria-hidden');
}

async function loadLogs(importId) {
    const url = `/imports/${importId}/logs`;
    try {
        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error("Network response was not ok");
        }

        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        return null;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const buttons = document.querySelectorAll(".show-import-logs-btn");

    buttons.forEach(button => {
        button.addEventListener("click", async () => {
            const importId = button.getAttribute("data-log-id");

            let data = await loadLogs(importId);
            openModal("importLogsModal");
            const modalContent = document.getElementById("importLogsModalContent");
            modalContent.innerHTML = ''

            if (data.logs.length > 0) {
                let html = `<h5>${data.title}</h5>`;

                data.logs.forEach(log => {
                    html += `
                        <table class="table table-bordered table-striped mb-4">
                            <tr>
                                <th>Row Number</th>
                                <td>${log.number ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Column</th>
                                <td>${log.column ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Value</th>
                                <td><pre>${log.value ?? ''}</pre></td>
                            </tr>
                            <tr>
                                <th>Message</th>
                                <td><pre>${log.message ?? ''}</pre></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>${log.created ?? ''}</td>
                            </tr>
                        </table>
                    `;
                });

                modalContent.innerHTML = html;
                return;
            } else {
                modalContent.innerHTML = `<p class="text-danger text-center">No logs for this import.</p>`;
                return;
            }
        });
    });
});
