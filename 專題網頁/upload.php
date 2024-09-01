<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel檔案上傳</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
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
        /* 确保所有单元格都对齐 */
        .align-cell {
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>請上傳Excel檔案</h1>
    <input type="file" id="uploadExcel" />
    <button id="uploadButton">上傳</button>
    <div id="uploadStatus"></div>
    <table id="uploadedData">
        <thead>
            <tr>
                <th>序號</th>
                <th>標號</th>
                <th>圖號</th>
                <th>版次</th>
                <th>類別</th>
                <th>圖名</th>
                <th>CAD檔名</th>
                <th>影像檔名</th>
                <th>狀態</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        let jsonData = [];
        
        document.getElementById('uploadExcel').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function(event) {
                var data = new Uint8Array(event.target.result);
                var workbook = XLSX.read(data, { type: 'array' });
                var firstSheetName = workbook.SheetNames[0];
                var worksheet = workbook.Sheets[firstSheetName];
                jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                console.log('JSON Data:', jsonData);
            };
            reader.readAsArrayBuffer(file);
        });

        document.getElementById('uploadButton').addEventListener('click', function() {
            if (jsonData.length === 0) {
                alert('請先選擇一個檔案。');
                return;
            }

            const headers = jsonData[0];
            const dataRows = jsonData.slice(1);
            const tableBody = document.querySelector('#uploadedData tbody');
            tableBody.innerHTML = ''; // 清除之前的数据

            const columnMapping = {
                "標號": "serial",
                "圖號": "number",
                "版次": "edition",
                "類別": "category",
                "圖名": "image",
                "CAD檔名": "CAD",
                "影像檔名": "file"
            };

            dataRows.forEach((row, index) => {
                if (row.length < 7) {
                    console.warn('第 ' + (index + 1) + ' 行缺少欄位。');
                    return;
                }

                let dataToUpload = headers.reduce((acc, header, idx) => {
                    const dynamoDBColumn = columnMapping[header];
                    if (dynamoDBColumn) {
                        acc[dynamoDBColumn] = (row[idx] || '').toString();
                    }
                    return acc;
                }, { index: index.toString() });

                const tableRow = document.createElement('tr');
                tableRow.innerHTML = `
                    <td class="align-cell">${index + 1}</td>
                    <td class="align-cell">${dataToUpload.serial || ''}</td>
                    <td class="align-cell">${dataToUpload.number || ''}</td>
                    <td class="align-cell">${dataToUpload.edition || ''}</td>
                    <td class="align-cell">${dataToUpload.category || ''}</td>
                    <td class="align-cell">${dataToUpload.image || ''}</td>
                    <td class="align-cell">${dataToUpload.CAD || ''}</td>
                    <td class="align-cell">${dataToUpload.file || ''}</td>
                    <td class="align-cell">Uploading...</td>
                `;
                tableBody.appendChild(tableRow);

                fetch('proxy.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataToUpload)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Success:', data);
                    tableRow.cells[8].textContent = 'Success';
                    tableRow.cells[8].style.color = 'green';
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableRow.cells[8].textContent = 'Error';
                    tableRow.cells[8].style.color = 'red';
                });
            });

            document.getElementById('uploadStatus').textContent = '檔案上傳成功，請確認狀態。';
        });
    </script>
</body>
</html>