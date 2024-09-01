<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>檔案上傳至S3</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>請上傳PDF或圖片檔案</h1>
    <input type="file" id="uploadFiles" accept=".pdf, .jpg, .jpeg, .png" multiple />
    <button id="uploadButton">上傳</button>
    <div id="uploadStatus"></div>
    <table id="uploadedData">
        <thead>
            <tr>
                <th>檔案名稱</th>
                <th>狀態</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
       document.getElementById('uploadButton').addEventListener('click', function() {
            const fileInput = document.getElementById('uploadFiles');
            if (fileInput.files.length === 0) {
                alert('Please select files first.');
                return;
            }

            const files = fileInput.files;
            const formData = new FormData();
            for (let i = 0; i < files.length; i++) {
                formData.append('files[]', files[i]);
            }

            fetch('proxy2.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                const statusDiv = document.getElementById('uploadStatus');
                statusDiv.textContent = 'Files uploaded successfully!';
                
                // Update table with uploaded file data
                const tbody = document.getElementById('uploadedData').getElementsByTagName('tbody')[0];
                data.forEach((item, index) => {
                    const row = tbody.insertRow();
                    const nameCell = row.insertCell(0);
                    const statusCell = row.insertCell(1);
                    
                    nameCell.textContent = item.name || `File ${index + 1}`;
                    statusCell.textContent = item.status || 'Failed';
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('uploadStatus').textContent = 'Error uploading files.';
            });
        });
    </script>
</body>
</html>