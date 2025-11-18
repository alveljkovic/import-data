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

async function loadAudits(importType, fileKey, rowId) {
    const url = `/imported-data/${importType}/${fileKey}/${rowId}/audits`;
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

    const buttons = document.querySelectorAll(".show-audits-btn");

    buttons.forEach(button => {
        button.addEventListener("click", async () => {
            const rowId = button.getAttribute("data-row-id");
            const importType = button.getAttribute("data-import-type");
            const fileKey = button.getAttribute("data-file-key");

            let data = await loadAudits(importType, fileKey, rowId);

            openModal("auditModal");

            const modalContent = document.getElementById("auditModalContent");
            modalContent.innerHTML = ''

            if (data.audits.length > 0) {
                let html = `<h5>${data.title}</h5>`;

                data.audits.forEach(audit => {
                    html += `
                        <table class="table table-bordered table-striped mb-4">
                            <tr>
                                <th>Row Number</th>
                                <td>${audit.number ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Table</th>
                                <td>${audit.table ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Column</th>
                                <td>${audit.column ?? 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Old Value</th>
                                <td><pre>${audit.old ?? ''}</pre></td>
                            </tr>
                            <tr>
                                <th>New Value</th>
                                <td><pre>${audit.new ?? ''}</pre></td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>${audit.created_at ?? ''}</td>
                            </tr>
                        </table>
                    `;
                });

                modalContent.innerHTML = html;
                return;
            } else {
                modalContent.innerHTML = `<p class="text-danger text-center">No audits for this row.</p>`;
                return;
            }
            
        });
    });

});
