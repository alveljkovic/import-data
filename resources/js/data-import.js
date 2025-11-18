document.addEventListener('DOMContentLoaded', () => {
    // Fetching the hidden element containing configurations
    const configDataElement = document.getElementById('data-import-config');
    
    if (!configDataElement) {
        // Exit if the element is not found (page is not Data Import)
        return; 
    }

    // Parsing the JSON configuration
    let configs;
    try {
        configs = JSON.parse(configDataElement.getAttribute('data-configs'));
    } catch (e) {
        console.error('Failed to parse data-configs attribute:', e);
        return;
    }

    // Fetching DOM elements
    const container = document.getElementById('file-inputs-container');
    const select = document.getElementById('import_type_select');

    /**
     * Function called when the selected option changes.
     */
    const handleSelectChange = () => {
        const selectedType = select.value;
        // Clearing previous content
        container.innerHTML = ''; 

        if (selectedType && configs[selectedType]) {
            const files = configs[selectedType]['files'];
            
            // Taking the first defined file key, as the form only submits one file
            const fileKeys = Object.keys(files);
            let fileHtml = '';

            fileKeys.forEach(key => {
                fileHtml += createFileInputSection(key, files);
            });

            container.innerHTML = fileHtml;
        }
    };

    /**
     * Function for generating the HTML for a file input section.
     * @param {*} fileKey - key of the file configuration
     * @param {*} files - files configuration object
     * @returns 
     */
    const createFileInputSection = (fileKey, files) => {
        const fileConfig = files[fileKey];

        if (fileConfig) {
            // Generating a list of required headers
            const headers = Object.keys(fileConfig.headers_to_db).join(', ');
            
            // Creating the HTML string
            return `
                <div class="form-group border p-3 mt-3">
                    <label for="import_file">DS Sheet: ${fileConfig.label}</label>
                    <input type="file" name="import_file_${fileKey}" id="import_file_${fileKey}" class="form-control" accept=".csv, .xlsx, .xls">
                    
                    <small class="form-text text-muted mt-2">
                        <strong>Required Headers:</strong> ${headers}
                    </small>
                </div>
            `;
        }
    }

    // Adding the event listener and initial trigger
    select.addEventListener('change', handleSelectChange);
    
    // Trigger the function immediately after loading to initialize the form
    handleSelectChange(); 
});
